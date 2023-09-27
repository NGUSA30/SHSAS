<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="mchongo.css">
    <link rel="stylesheet" href="selform.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    #table {
        margin-top: 20px;
    }

    table {
        width: 75%;
        border-collapse: collapse;
        font-size: small;
        margin-left: 200px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:nth-child(odd) {
        background-color: #ffffff;
    }

    /* Add zebra-striping effect */
    .zebra-row {
        background-color: #f7f7f7;
    }

    /* Add styles for the p tag */
    p {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    #k2{
    text-align: center;
}
#k3{
    text-align: center;
}
</style>
</head>
<body>
<div id="top">
        <div id="inner">
    <span id="ofisi">OFISI YA RAISI</span><br>
    <span>TAWALA ZA MIKOA NA SERIKALI ZA MITAA</span><br>
     <IMG src="IMAGES/school.png" id="img" alt="picture">
   <p id="head">STUDENT HIGH SCHOOL ALLOCATION SYSTEM</p>
   <hr color="gray">
        </div>
   </div>
   <div id="ovar">
   <div id="side">
    <a href="selform.php" ><img src="IMAGES/dashboard.png" alt="" id="icon1" title="dashboard"></a><br>
    <a href="form.php"><img src="IMAGES/google-forms.png" alt="" id="icon2" title="selform"></a><br>
    <a href="allocation.php" ><img src="IMAGES/allocation.png" alt="" id="icon3" title="myAllocation"><br>
     <a href="change.php"><img src="IMAGES/password.png" alt="" id="icon4" title="change password"></a><br>
     <a href="login.php"><img src="IMAGES/power-off.png" alt="" id="icon5" title="Logout"></a>
   </div>
   <div id="content">
<?php
session_start();
include('conn.php');

// Check if admission details are available in the session
if (isset($_SESSION['admissionDetails']) && !empty($_SESSION['admissionDetails'])) {
    $admissionDetails = $_SESSION['admissionDetails'];
    $enock=$_SESSION['mtani'];
    // Display the admission details in a table
    echo "<h3 id='k2'>Dear $enock ,You are already admitted</h3>";
    echo '<h2 id="k3">Admission Details</h2>';
    echo '<div id="table">';
    echo '<table>';
    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
    
    foreach ($admissionDetails as $row) {
        echo '<tr>';
        echo '<td>' . $row['schoolName'] . '</td>';
        echo '<td>' . $row['combination'] . '</td>';
        echo '<td>' . $row['fullName'] . '</td>';
        echo '<td>' . $row['region'] . '</td>';
        echo '<td>' . $row['CNo'] . '</td>';
        echo '<td>' . $row['email'] . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    echo '</div>';
} else {
    // Admission details not found in the session
    echo '<p>No admission details found.</p>';
}
$conn->close();
?>
   </div>
   </div>
   <div id="footer5">
    <p>Copyright ©2023 Students HghSchool Allocation System. All Rights Reserved.</p>
   </div>
</body>
</html>