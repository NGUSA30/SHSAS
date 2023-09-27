<?php
$servername="localhost";
$username="root";
$password="";
$dbname="allocation_db";
$conn=new mysqli($servername,$username,$password,$dbname);
if($conn->connect_error==true){
    echo "error while connecting";
}
?>