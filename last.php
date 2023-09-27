<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="mchongo.css">
    <link rel="stylesheet" href="selform.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <style>
/* Apply zebra-striping to the table rows */
table {
    width: 85%;
    border-collapse: collapse;
    font-family: serif;
    margin-left:50px;
    text-align: center;
}

table th, table td {
    padding: 8px;
    text-align: left;
    border: 1px solid gray;
}

table th {
    background-color: #f2f2f2; /* Header row background color */
}

/* Apply alternating background colors to table rows */
table tr:nth-child(even) {
    background-color: white; /* Even row background color */
}

table tr:nth-child(odd) {
    background-color: whitesmoke; /* Odd row background color */
}
h2{
    text-align: center;
}
@media print {
            #printButton {
                display: none;
            }
            #side{
                display:none;
            }
            #footer{
                display:none;
            }
        }
        #printButton{
            padding:5px;
            font-family: serif;
            text-transform: uppercase;
            margin-top: 15px;
            border-radius: 0.5em;
            margin-left:45px;
            font-weight: bolder;
            cursor: pointer;

        }
        .error-message{
            font-family: serif;
    background-color: rgb(35, 35, 39);
    color:white;
    padding:20px;
    border-radius: 0.4em;
        }
</style>
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
    <a href="" ><img src="IMAGES/allocation.png" alt="" id="icon3" title="myAllocation"><br>
     <a href="change.php"><img src="IMAGES/password.png" alt="" id="icon4" title="change password"></a><br>
     <a href="login.php"><img src="IMAGES/power-off.png" alt="" id="icon5" title="Logout"></a>
   </div>
   <div id="content">
   <?php
   session_start();
include('conn.php');
if (isset($_POST['submit1'])) {
    $searchTerm = $_POST['result'];

    // Use prepared statement to prevent SQL injection
    $query = "SELECT * FROM allocation WHERE schoolName = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>$searchTerm ADMITTED STUDENTS</h2>";
        echo "<table border='1'>";
        echo "<tr>
        <th>allocationID</th>
        <th>School Name</th>
        <th>Combination</th>
        <th>FullName</th>
        <th>Region</th>
        <th>Candidate No</th>
        <th>Email</th>
        </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['allocationID'] . "</td>";
            // Output other columns as needed
            echo "<td>" . $row['schoolName'] . "</td>";
            echo "<td>" . $row['combination'] . "</td>";
            echo "<td>" . $row['fullName'] . "</td>";
            echo "<td>" . $row['region'] . "</td>";
            echo "<td>" . $row['CNo'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo '<button id="printButton">PRINT</button>';


    } else {
        echo '<div class="error-message">No results found for the given school name.</div>';
    
    }

    $stmt->close();
}

$conn->close();
?>
<?php
?>
<script>
    // Function to print the table
    function printTable() {
        window.print();
    }

    // Add click event listener to the print button
    document.getElementById("printButton").addEventListener("click", printTable);
</script>
   </div>
   </div>
   <div id="footer5">
    <p>Copyright ©2023 Students HghSchool Allocation System. All Rights Reserved.</p>
   </div>
</body>
</html>