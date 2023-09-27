<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "allocation_db";

// Create a new mysqli connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "Error while connecting";
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['CNo'];
    $password = $_POST['password'];
    if ($username === "admin" && $password === "2003") {
        // Redirect to admin.php
        header("Location: admin.php");
        exit();
    }
    // Use prepared statements to prevent SQL injection
    $query = "SELECT CNo, password FROM students WHERE CNo = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION["CNo"] = $username;
        $_SESSION["password"] = $password;
        $stmt->close();
        $conn->close();
        header("Location: selform.php");
        exit();
    } else {
        $loginError = "Invalid username or password";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="project.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/school.png" alt="school">
    <title>Login</title>
</head>
<body>

<div class="myform">
    <?php if (isset($loginError)) { ?>
        <div class="notification"> Invalid username or password </div>
    <?php } ?>
    <div id="over">
        <div id="inner">
            <img src="IMAGES/school.png" alt="school allocation" id="image">
            <p id="head">STUDENTS HIGH SCHOOL ALLOCATION SYSTEM</p>
            <form  action="#" method="POST">
                <input type="text" placeholder="Candidate Number" id="index" name="CNo" required><br>
                <input type="password" placeholder="Password" id="password" name="password" required><br><br>
                <button type="submit" name="submit" id="submit" value="">Login</button><br><br>
                <img src="IMAGES/login.png" alt="login" id="login">
                <a href="change.php" id="link2">Forgot password?</a>
            </form>
        </div>
    </div>
    <footer>
        <p id="footer">Copyright&copy;2023. All rights reserved</p>
    </footer>
</body>
</html>
