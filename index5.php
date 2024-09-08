<?php
include "db.php"; // Ensure this file contains a valid database connection

// Initialize variables
$error = '';
$success = '';
$result = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $current_time = time();
    $DateTime = date("d-m-y H:i:s", $current_time);

    // Initialize success flag
    $isSuccess = true;

    // Check if barcode and text arrays are set
    if (isset($_POST['barcode']) && isset($_POST['text'])) {
        foreach ($_POST['barcode'] as $index => $barcode) {
            $barcode = mysqli_real_escape_string($connection, trim($barcode));
            $text = mysqli_real_escape_string($connection, trim($_POST['text'][$index] ?? ''));

            if (!empty($barcode)) {
                // Check for duplicate barcode
                $query_grap = "SELECT * FROM item WHERE barcode = '$barcode'";
                $query_grap_exe = mysqli_query($connection, $query_grap);

                if (!$query_grap_exe) {
                    $error = "Database error: " . mysqli_error($connection);
                    $isSuccess = false;
                    break; // Exit loop on error
                }

                $count = mysqli_num_rows($query_grap_exe);

                if ($count > 0) {
                    $error = "Duplicate data for barcode: $barcode";
                    $isSuccess = false;
                    break; // Exit loop on duplicate
                } else {
                    // Insert new record
                    $query = "INSERT INTO item (barcode, datereg" . (!empty($text) ? ", text" : "") . ") VALUES ('$barcode', '$DateTime'" . (!empty($text) ? ", '$text'" : "") . ")";
                    $query_exe = mysqli_query($connection, $query);

                    if (!$query_exe) {
                        $error = "Database error: " . mysqli_error($connection);
                        $isSuccess = false;
                        break; // Exit loop on error
                    }
                }
            }
        }

        // Set success message if there were no errors
        if ($isSuccess) {
            $success = "Data saved successfully!";
            // Redirect to avoid form resubmission
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        $error = "No barcode or text data received.";
    }
}

// Fetch all items from the database
$query = "SELECT * FROM item ORDER BY id DESC";
$result = mysqli_query($connection, $query);

// Check if the query was successful
if (!$result) {
    $error = "Database error: " . mysqli_error($connection);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Scanner Form</title>
    <style>
        /* Basic styling for the form and buttons */
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type="text"] {
            width: calc(50% - 22px);
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .alert {
            margin-top: 10px;
            padding: 10px;
            color: white;
            border-radius: 5px;
        }

        .alert-danger {
            background-color: #f44336;
        }

        .alert-success {
            background-color: #4CAF50;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Barcode Scanner Form</h2>
        <form id="barcodeForm" method="POST" action="">
            <!-- Input Fields for Barcodes and Additional Data -->
            <?php
            // Output the input fields with previous values (if any)
            $barcodeValues = isset($_POST['barcode']) ? $_POST['barcode'] : array_fill(0, 5, '');
            $textValues = isset($_POST['text']) ? $_POST['text'] : array_fill(0, 5, '');
            for ($i = 0; $i < 2; $i++): ?>
                <div class="form-group">
                    <input type="text" name="barcode[]" placeholder="Scan barcode here" value="<?php echo htmlspecialchars($barcodeValues[$i]); ?>" class="barcode-input" autofocus>
                    <input type="text" name="text[]" placeholder="Additional data" value="<?php echo htmlspecialchars($textValues[$i]); ?>">
                </div>
            <?php endfor; ?>

            <div class="form-group">
                <button type="submit" name="submit">Submit</button>
            </div>
        </form>

        <!-- Display alert messages -->
        <div id="message">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
        </div>

        <!-- Table to display barcode data -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Barcode</th>
                    <th>Date Registered</th>
                    <th>Text</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['barcode']); ?></td>
                            <td><?php echo htmlspecialchars($row['datereg']); ?></td>
                            <td><?php echo htmlspecialchars($row['text']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        // JavaScript to prevent form from automatically submitting
        document.getElementById('barcodeForm').addEventListener('submit', function(event) {
            // Get all input fields
            const barcodeInputs = document.querySelectorAll('input[name="barcode[]"]');
            const textInputs = document.querySelectorAll('input[name="text[]"]');

            let allFilled = true;

            // Check if all barcode and text inputs are filled
            barcodeInputs.forEach((input, index) => {
                if (input.value.trim() === '' || textInputs[index].value.trim() === '') {
                    allFilled = false;
                }
            });

            if (!allFilled) {
                event.preventDefault(); // Prevent form submission
                // Optionally, you can also display a message to the user
                console.log('Please fill in all fields.');
            }
        });
    </script>
</body>

</html>