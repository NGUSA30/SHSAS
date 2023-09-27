<?php 
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "allocation_db";

$conn = new mysqli($servername, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['email'];
    $passwords = $_POST['passwords'];
    $cno = $_POST['cno'];
    $fullname=$_POST['fullName'];
    $gender=$_POST['gender'];
    $phonenumber=$_POST['phone'];
    // Start a transaction to ensure data consistency across tables
    mysqli_autocommit($conn, false);
    $success = true;

    // Insert email into the students table
    $query = "INSERT INTO students (email, password, CNo, FullName, gender, Phonenumber) VALUES ('$username', '$passwords', '$cno', '$fullname', '$gender', '$phonenumber')";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        $success = false;
    }

    if ($success) {
        // Commit the transaction if the insert into students was successful
        mysqli_commit($conn);
        header("location: login.php");
        exit();
    } else {
        // Rollback the transaction if the insert into students failed
        mysqli_rollback($conn);
        $loginError = "Something went wrong";
    }

    // Reset autocommit mode
    mysqli_autocommit($conn, true);
} else {
    $loginError = "Invalid request method";
}

mysqli_close($conn);