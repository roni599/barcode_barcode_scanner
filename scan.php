<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Scan</title>
    <link rel="stylesheet" href="./assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group button {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #fff;
        }

        .alert-warning {
            background-color: #ffc107;
            color: #212529;
        }
    </style>
</head>

<body onload="document.scanForm.barcode.focus();">
    <div class="container">
        <h2>Scan Barcode</h2>
        <form name="scanForm" method="POST" action="">
            <div class="form-group inputdata">
                <input type="text" name="barcode" placeholder="Scan barcode here" required>
            </div>
            <div class="form-group">
                <button type="submit">Search</button>
            </div>
        </form>

        <?php
        include "db.php";
        session_start();

        // Initialize variables
        $barcode_search_result = null;

        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['barcode'])) {
            $barcode = mysqli_real_escape_string($connection, $_POST['barcode']);

            // Search for the barcode in the database
            $query_grap = "SELECT * FROM item WHERE barcode = '$barcode'";
            $barcode_search_result = mysqli_query($connection, $query_grap);
            $count = mysqli_num_rows($barcode_search_result);
        }

        // Display search result
        if ($barcode_search_result && mysqli_num_rows($barcode_search_result) > 0) {
            echo "<h3>Barcode Found:</h3>";
            echo "<table>";
            echo "<thead><tr><th>ID</th><th>Barcode</th><th>Date Registered</th></tr></thead>";
            echo "<tbody>";
            while ($row = mysqli_fetch_assoc($barcode_search_result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['barcode'] . "</td>";
                echo "<td>" . $row['datereg'] . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo "<div class='alert alert-warning'>Barcode not found in the database.</div>";
        }
        ?>
    </div>
</body>

</html>
