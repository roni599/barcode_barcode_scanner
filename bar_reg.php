<?php
include "db.php";
session_start(); // Start the session to store messages

if (isset($_POST['barcode'])) {
    $barcode = mysqli_real_escape_string($connection, $_POST['barcode']);
    $current_time = time();
    $DateTime = date("d-m-y H:i:s", $current_time);  // Using date() instead of strftime()

    $query_grap = "SELECT * FROM item WHERE barcode = '$barcode'";
    $query_grap_exe = mysqli_query($connection, $query_grap);
    $count = mysqli_num_rows($query_grap_exe);

    if ($count > 0) {
        $_SESSION['error'] = "Data Duplicated!!";
    } else {
        $query = "INSERT INTO item (barcode, datereg) VALUES ('$barcode', '$DateTime')";
        $query_exe = mysqli_query($connection, $query);

        if ($query_exe) {
            $_SESSION['success'] = "Data Inserted Successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($connection);
        }
    }

    // Redirect back to the form page
    header("Location: index.php");
    exit();
}
?>
