<?php
    // Database connection details
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'barcode';

    // Create a connection
    $connection = mysqli_connect($host, $username, $password, $database);

    // Check if the connection was successful
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Optionally, you can set the character set to UTF-8
    mysqli_set_charset($connection, "utf8");
?>
