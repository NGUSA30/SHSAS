<?php
session_start();
$servername="localhost";
$username="root";
$password="";
$dbname="allocation_db";
$conn=new mysqli($servername,$username,$password,$dbname);
if($conn->connect_error==true){
    echo "error while connecting";
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['index'];
    $password = $_POST['password'];
    $query = "SELECT password FROM students WHERE CNo = '$username'";
    $result = mysqli_query($conn , $query);
  $_SESSION["password"] = $password;
  $_SESSION["password"] = $password;
            header("Location: selform.php");
            exit();
        } else {
            $loginError = "Invalid username or password";
        }
mysqli_close($conn);
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

?>