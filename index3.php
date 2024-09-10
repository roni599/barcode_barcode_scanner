<?php
include "db.php";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['barcodes'])) {
    $current_time = time();
    $DateTime = date("d-m-y H:i:s", $current_time);

    $barcodes = $_POST['barcodes'];
    $prices = $_POST['prices'];
    $quantities = $_POST['quantities'];

    foreach ($barcodes as $index => $barcode) {
        $barcode = mysqli_real_escape_string($connection, $barcode);
        $price = mysqli_real_escape_string($connection, $prices[$index]);
        $quantity = mysqli_real_escape_string($connection, $quantities[$index]);

        $query_grap = "SELECT * FROM item WHERE barcode = '$barcode'";
        $query_grap_exe = mysqli_query($connection, $query_grap);

        if (!$query_grap_exe) {
            $error = "Database error: " . mysqli_error($connection);
            break;
        }

        $count = mysqli_num_rows($query_grap_exe);

        if ($count > 0) {
            $error = "Duplicate data for barcode: $barcode";
        } else {
            $query = "INSERT INTO item (barcode, price, quantity, datereg) VALUES ('$barcode', '$price', '$quantity', '$DateTime')";
            $query_exe = mysqli_query($connection, $query);

            if ($query_exe) {
                $success = "Data saved successfully!";
            } else {
                $error = "Database error: " . mysqli_error($connection);
            }
        }
    }

    if (!$error) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$query = "SELECT * FROM item ORDER BY id DESC";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($connection));
}
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

        .input-group {
            margin-bottom: 10px;
        }

        input[type="text"] {
            width: calc(33% - 10px);
            padding: 10px;
            margin-right: 5px;
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
            display: none;
            width: 98%;
        }

        button.show {
            display: inline-block;
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
        <h2>E-pos System</h2>
        <form method="POST" action="">
            <div id="barcode-container">
            </div>
            <div class="form-group">
                <button id="submit-button" type="submit">Submit</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <!-- Table to display barcode data -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Barcode</th>
                    <th>Price</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['barcode']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <!-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            const barcodeContainer = document.getElementById('barcode-container');
            const submitButton = document.getElementById('submit-button');

            let barcodeInput = null;
            let inputTimeout = null;

            document.addEventListener("keydown", function(e) {
                if (!barcodeInput) {
                    console.log("object")
                    createBarcodeInput();
                    barcodeInput.focus();
                }

                // Handle Enter key only if input is valid and not from scanner
                if (e.key === "Enter" && barcodeInput && barcodeInput.value.trim() !== "") {
                    e.preventDefault();
                    addBarcodeFields(barcodeInput.value);
                    barcodeInput.value = "";
                    barcodeInput = null; // Clear barcodeInput reference
                }

                // Clear previous timeout if any
                if (inputTimeout) {
                    clearTimeout(inputTimeout);
                }

                // Set timeout to determine if the input is coming from scanner
                inputTimeout = setTimeout(function() {
                    if (barcodeInput && barcodeInput.value.trim() === "") {
                        // If no input, it's likely not from scanner, remove the input field
                        barcodeContainer.removeChild(barcodeInput);
                        barcodeInput = null;
                    }
                }, 1000); // 1 second delay to check if input is from scanner
            });

            function createBarcodeInput() {
                barcodeInput = document.createElement("input");
                barcodeInput.type = "text";
                barcodeInput.id = "barcode-input";
                barcodeInput.placeholder = "Scan barcode here";
                barcodeInput.autocomplete = "off";
                barcodeContainer.appendChild(barcodeInput);

                submitButton.classList.add('show');
            }

            function addBarcodeFields(barcode) {
                const inputGroup = document.createElement("div");
                inputGroup.className = "input-group";

                // Barcode input field
                const barcodeField = document.createElement("input");
                barcodeField.type = "text";
                barcodeField.name = "barcodes[]";
                barcodeField.value = barcode;
                barcodeField.readOnly = true;

                // Price input field
                const priceField = document.createElement("input");
                priceField.type = "text";
                priceField.name = "prices[]";
                priceField.placeholder = "Enter price";

                // Quantity input field
                const quantityField = document.createElement("input");
                quantityField.type = "text";
                quantityField.name = "quantities[]";
                quantityField.placeholder = "Enter quantity";

                inputGroup.appendChild(barcodeField);
                inputGroup.appendChild(priceField);
                inputGroup.appendChild(quantityField);

                barcodeContainer.insertBefore(inputGroup, document.getElementById('barcode-input'));

                // Remove barcode input field
                barcodeContainer.removeChild(barcodeInput);

                // Reset barcodeInput variable
                barcodeInput = null;
            }
        });
    </script> -->

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const barcodeContainer = document.getElementById('barcode-container');
        const submitButton = document.getElementById('submit-button');

        let barcodeInput = null;
        let lastKeyTime = Date.now();  // Track the time of the last key press
        let inputBuffer = '';          // Buffer for barcode input
        const scannerSpeedThreshold = 30;  // Milliseconds threshold to detect barcode scanner input

        document.addEventListener("keydown", function(e) {
            const currentTime = Date.now();
            const timeDiff = currentTime - lastKeyTime;  // Calculate time difference between key presses
            lastKeyTime = currentTime;

            // Check if the time difference between key presses is less than the threshold
            if (timeDiff < scannerSpeedThreshold) {
                inputBuffer += e.key;  // Add the current key to the input buffer

                if (!barcodeInput) {
                    createBarcodeInput();  // Only create the input field when barcode scanner is detected
                    barcodeInput.focus();
                }
            }

            // If the Enter key is pressed, finalize the barcode input
            if (e.key === "Enter" && barcodeInput && inputBuffer.trim() !== "") {
                e.preventDefault();
                addBarcodeFields(inputBuffer.trim());  // Pass the entire scanned barcode value
                inputBuffer = '';  // Clear the buffer after the input is processed
            }
        });

        // Create a new input field for barcode scanner input
        function createBarcodeInput() {
            barcodeInput = document.createElement("input");
            barcodeInput.type = "text";
            barcodeInput.id = "barcode-input";
            barcodeInput.placeholder = "Scan barcode here";
            barcodeInput.autocomplete = "off";
            barcodeContainer.appendChild(barcodeInput);

            submitButton.classList.add('show');  // Show the submit button
        }

        // Add barcode, price, and quantity fields when a barcode is scanned
        function addBarcodeFields(barcode) {
            const inputGroup = document.createElement("div");
            inputGroup.className = "input-group";

            // Barcode input field
            const barcodeField = document.createElement("input");
            barcodeField.type = "text";
            barcodeField.name = "barcodes[]";
            barcodeField.value = barcode;
            barcodeField.readOnly = true;

            // Price input field
            const priceField = document.createElement("input");
            priceField.type = "text";
            priceField.name = "prices[]";
            priceField.placeholder = "Enter price";

            // Quantity input field
            const quantityField = document.createElement("input");
            quantityField.type = "text";
            quantityField.name = "quantities[]";
            quantityField.placeholder = "Enter quantity";

            inputGroup.appendChild(barcodeField);
            inputGroup.appendChild(priceField);
            inputGroup.appendChild(quantityField);

            barcodeContainer.insertBefore(inputGroup, document.getElementById('barcode-input'));

            // Remove the barcode input field after processing the scanned input
            if (barcodeInput) {
                barcodeContainer.removeChild(barcodeInput);
            }

            barcodeInput = null;  // Clear the input reference
        }
    });
</script>


</body>

</html>