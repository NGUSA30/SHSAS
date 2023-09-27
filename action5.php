<?php
include('conn.php');
$password1=$_POST['oldpassword'];
$password2=$_POST['newpassword'];
$email=$_POST['email'];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm'])) {
    // Validate email, oldPassword, and newPassword (you can add more validation)
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password1) && !empty($password2)) {
        // Check if the email and old password exist in the database
        $query = "SELECT * FROM students WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $email, $password1);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Update the password with the new one
            $updateQuery = "UPDATE students SET password = ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ss", $password2, $email);

            if ($updateStmt->execute()) {
                echo "Password updated successfully.";
            } else {
                echo "Error updating password: " . $conn->error;
            }
        } else {
            echo "Invalid email or old password.";
        }

        $stmt->close();
    } else {
        echo "Invalid input data.";
    }
}

?>