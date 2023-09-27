<?php
// Include your database connection file (conn.php)
include('conn.php');
session_start();

if (isset($_POST['submit'])) {
    // Get user-entered email and password
    $email = $_POST['indexnumber'];
    $password = $_POST['password'];

    // Validate the email (you can add more validation if needed)
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // SQL query to check if the email and password match a record in the "student" table
        $query = "SELECT * FROM students WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Login successful
            // You can store user information in sessions if needed

            // Redirect to the dashboard page
            header("Location: selform.php");
            exit();
        } else {
            // Login failed
            $error_message = "Incorrect email or password. Please try again.";
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        $error_message = "Invalid email format.";
    }
}
?>