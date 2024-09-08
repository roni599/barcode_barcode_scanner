<?php
include "db.php";

// Initialize variables
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['barcode'])) {
    $barcode = $_POST['barcode'];

    // Sanitize input
    $barcode = mysqli_real_escape_string($connection, $barcode);

    // Check for duplicate barcode
    $query_grap = "SELECT * FROM item WHERE barcode = '$barcode'";
    $query_grap_exe = mysqli_query($connection, $query_grap);
    $count = mysqli_num_rows($query_grap_exe);

    if ($count > 0) {
        $error = "Data Duplicated!!";
    } else {
        // Insert new record
        $current_time = time();
        $DateTime = date("d-m-y H:i:s", $current_time);
        $query = "INSERT INTO item (barcode, datereg) VALUES ('$barcode', '$DateTime')";
        $query_exe = mysqli_query($connection, $query);

        if ($query_exe) {
            $success = "Data saved successfully!";
        } else {
            $error = "Database error: " . mysqli_error($connection);
        }

        // Redirect to the same page to avoid resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Fetch all items from the database
$query = "SELECT * FROM item ORDER BY id DESC";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-pos</title>
    <style>
        /* Basic styling for the form and table */
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector('form');
            const barcodeInput = document.querySelector('input[name="barcode"]');

            // Prevent form submission when Enter key is pressed
            barcodeInput.addEventListener("keypress", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                }
            });

            // Allow form submission only when clicking the Submit button
            form.addEventListener("submit", function(e) {
                if (!barcodeInput.value) {
                    e.preventDefault(); // Prevent submission if barcode input is empty
                }
            });
        });
    </script>
</head>

<body onload="document.getElementById('barcode-input').focus();">
    <div class="container">
        <h2>E-pos System</h2>
        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="barcode" id="barcode-input" placeholder="Scan barcode here" required>
            </div>
            <div class="form-group">
                <button type="submit">Submit</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Table to display barcode data -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Barcode</th>
                    <th>Date Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['barcode']); ?></td>
                        <td><?php echo htmlspecialchars($row['datereg']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>