<?php
include "db.php";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['barcodes'])) {
    $current_time = time();
    $DateTime = date("d-m-y H:i:s", $current_time);

    $barcodes = $_POST['barcodes'];

    foreach ($barcodes as $barcode) {
        if (!empty($barcode)) {
            $barcode = mysqli_real_escape_string($connection, $barcode);

            $query_grap = "SELECT * FROM item WHERE barcode = '$barcode'";
            $query_grap_exe = mysqli_query($connection, $query_grap);
            $count = mysqli_num_rows($query_grap_exe);

            if ($count > 0) {
                $error = "Duplicate data for barcode: $barcode";
            } else {
                $query = "INSERT INTO item (barcode, datereg) VALUES ('$barcode', '$DateTime')";
                $query_exe = mysqli_query($connection, $query);

                if ($query_exe) {
                    $success = "Data saved successfully!";
                } else {
                    $error = "Database error: " . mysqli_error($connection);
                }
            }
        }
    }

    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

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

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const barcodeInputs = document.querySelectorAll('.barcode-input');

            // Focus on the first input field initially
            focusFirstEmptyInput();

            // Event listener for handling barcode scanning
            document.addEventListener("keypress", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    const currentInput = findFirstEmptyInput();
                    if (currentInput) {
                        currentInput.focus();
                    }
                }
            });

            // Function to find the first empty input field
            function findFirstEmptyInput() {
                for (let i = 0; i < barcodeInputs.length; i++) {
                    if (barcodeInputs[i].value.trim() === "") {
                        return barcodeInputs[i];
                    }
                }
                return barcodeInputs[barcodeInputs.length - 1];
            }

            // Function to focus on the first empty input field
            function focusFirstEmptyInput() {
                const firstEmptyInput = findFirstEmptyInput();
                if (firstEmptyInput) {
                    firstEmptyInput.focus();
                }
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>E-pos System</h2>
        <form method="POST" action="">
            <div id="barcode-container">
                <!-- Initialize multiple input fields -->
                <input type="text" name="barcodes[]" class="barcode-input" placeholder="Scan barcode here" required>
                <input type="text" name="barcodes[]" class="barcode-input" placeholder="Scan barcode here">
                <input type="text" name="barcodes[]" class="barcode-input" placeholder="Scan barcode here">
                <input type="text" name="barcodes[]" class="barcode-input" placeholder="Scan barcode here">
                <input type="text" name="barcodes[]" class="barcode-input" placeholder="Scan barcode here">
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
