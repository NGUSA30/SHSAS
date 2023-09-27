<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        #content {
    display: flex; /* Change to flex to make the content and side flex items */
    flex-wrap: nowrap;
    background-color: whitesmoke;
    font-family: serif;
    height: 32em;
    margin-left: 15px;
}
table {
            border-collapse: collapse;
            width: 100%;
            display: flex;
        }
        #go{
            text-decoration:none;
            color:blue;
            font-weight:bolder;
            padding:10px;
            margin-top:25em;
            
        }
        th, td {
            border: 1px solid gray;
            padding: 8px;
            text-align: left;
            font-size: small;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }


#tables{
    display:block;
    margin-top:10px;
    height:fit-content;
}
#side {
    width: fit-content; /* Set the width of the side navigation bar */
    background-color: #f2f2f2;
    padding: 10px;
    text-align: center;
    background-color: rgba(223, 223, 225, 0.5);
    height: 100vh;
}

#message-content {
    margin: 10px; /* Adjust margin as needed */
    background-color: lightblue; /* Define your desired background color */
    padding: 10px;
    font-weight: bolder;
    color: white;
    padding:10px;
}

    </style>
    <link rel="stylesheet" href="selform.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
<div id="content">
    <div id="side">
        <!-- Your side navigation bar content -->
        <a href="selform.php"><img src="IMAGES/dashboard.png" alt="" id="icon1" title="dashboard"></a><br>
        <a href="form.php"><img src="IMAGES/google-forms.png" alt="" id="icon2" title="selform"></a><br>
        <a href=""><img src="IMAGES/allocation.png" alt="" id="icon3" title="myAllocation"><br>
        <a href="change.php"><img src="IMAGES/password.png" alt="" id="icon4" title="change password"></a><br>
        <a href="login."><img src="IMAGES/power-off.png" alt="" id="icon5" title="Logout"></a>
    </div>
<?php
session_start();
include('conn.php');
$studentid = $_SESSION['number'];
$useremail=$_SESSION['email'];
$name=$_SESSION['name'];
$mkoa=$_SESSION['region'];
$school1=$_POST['School1'];
$school2=$_POST['School2'];
$comb1=$_POST['comb1'];
$comb2=$_POST['comb2'];
$comb3=$_POST['comb3'];
if($school1||$school2 == 'MINAKI' && $comb1 == 'HKL') {
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Kisw` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, `Hist`,`Kisw`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Kisw`,NULL AS `Lit_engl` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Geo', 'Kisw'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo $totalPoints;
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
        
    echo "</div>";
    // Close the database connection
    $stmt->close();
    }
}
    else if($school1||$school2 == 'MINAKI' && $comb1 == 'HGL') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, `Hist`,`Geo`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS `Lit_engl` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Hist', 'Geo', 'Lit_engl'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 8; // Adjust this value as needed
            echo $totalPoints;
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
               
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
                echo '<div id="table">';
                echo '<table>';
                echo '<tr><th>Candidate Number</th><th>School</th><th>Combination</th></tr>';
                echo '<tr><td>' . $studentid . '</td><td>' . $school1 . '</td><td>' . $comb1 . '</td></tr>';
                echo '</table>';
                echo '</div>';
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
            }
        } else {
            echo '<div id="message-content">Student results not found in the database.</div>';
        }
    echo "</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1||$school2 == 'MINAKI' && $comb1 == 'PCM') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`, NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Chem', 'B_Math'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           
            // Check if the student's total points meet the admission criteria
            $cutOffPoints =8; // Adjust this value as needed
            echo $totalPoints;
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
            }
            
        echo "</div>";
        // Close the database connection
        $stmt->close();
        }
    }
        
    else if($school1||$school2 == 'MINAKI' && $comb1 == 'PGM') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,`Geo`,`B_Math` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,`Geo`, `B_Math` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Geo`, `B_Math` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Geo', 'B_Math'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 8; // Adjust this value as needed
            echo $totalPoints;
            echo "<div id='tables'> ";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
            }
            
        echo "</div>";
        // Close the database connection
        $stmt->close();
        }
    }
        else if($school1||$school2 == 'MINAKI' && $comb2 == 'HGL') {
            // Connect to the database (assuming you have a $conn variable)
            // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
            $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
                      UNION ALL
                      SELECT 'business_results' AS 'table_name', `CNo`, `Hist`,`Geo`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
                      UNION ALL
                      SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS `Lit_engl` FROM `science_results` WHERE `CNo` = ?";
            $stmt = $conn->prepare($query);
            // Assuming you have stored the student's ID in a session variable
            $stmt->bind_param("sss", $studentid, $studentid, $studentid);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result->num_rows > 0) {
                // Initialize total points
                $totalPoints = 0;
        
                // Fetch the specific column data and table name
                $foundData = $result->fetch_assoc();
        
                // Calculate total points based on the grades (B=2, C=3)
                $subjects = ['Hist', 'Geo', 'Lit_engl'];
                foreach ($subjects as $subject) {
                    if (isset($foundData[$subject])) {
                        $grade = $foundData[$subject];
                        if ($grade == 'B') {
                            $totalPoints += 2;
                        } elseif ($grade == 'C') {
                            $totalPoints += 3;
                        }
                        elseif ($grade == 'D') {
                            $totalPoints += 4;
                        }
                        elseif ($grade == 'A') {
                            $totalPoints += 1;
                        }
                    }
                }
               
                // Check if the student's total points meet the admission criteria
                $cutOffPoints = 8; // Adjust this value as needed
                echo $totalPoints;
                echo "<div id='tables'>";
                if ($totalPoints <= $cutOffPoints) {
                   
                    echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>Candidate Number</th><th>School</th><th>Combination</th></tr>';
                    echo '<tr><td>' . $studentid . '</td><td>' . $school1 . '</td><td>' . $comb1 . '</td></tr>';
                    echo '</table>';
                    echo '</div>';
                } else {
                    echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
                }
            } else {
                echo '<div id="message-content">Student results not found in the database.</div>';
            }
        echo "<div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1||$school2 == 'MINAKI' && $comb2== 'HKL') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Kisw` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, `Hist`,`Kisw`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Kisw`,NULL AS `Lit_engl` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Hist', 'Geo', 'Kisw'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 8; // Adjust this value as needed
            echo $totalPoints;
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
            }
            
        echo "</div>";
        // Close the database connection
        $stmt->close();
        }
    }
       
       
        else if($school1||$school2 == 'MINAKI' && $comb2 == 'PCM') {
            // Connect to the database (assuming you have a $conn variable)
            // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
            $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
                      UNION ALL
                      SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`, NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
                      UNION ALL
                      SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
            $stmt = $conn->prepare($query);
            // Assuming you have stored the student's ID in a session variable
            $stmt->bind_param("sss", $studentid, $studentid, $studentid);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result->num_rows > 0) {
                // Initialize total points
                $totalPoints = 0;
        
                // Fetch the specific column data and table name
                $foundData = $result->fetch_assoc();
        
                // Calculate total points based on the grades (B=2, C=3)
                $subjects = ['Physics', 'Chem', 'B_Math'];
                foreach ($subjects as $subject) {
                    if (isset($foundData[$subject])) {
                        $grade = $foundData[$subject];
                        if ($grade == 'B') {
                            $totalPoints += 2;
                        } elseif ($grade == 'C') {
                            $totalPoints += 3;
                        }
                        elseif ($grade == 'D') {
                            $totalPoints += 4;
                        }
                        elseif ($grade == 'A') {
                            $totalPoints += 1;
                        }
                    }
                }
               
                // Check if the student's total points meet the admission criteria
                $cutOffPoints = 8; // Adjust this value as needed
                echo $totalPoints;
                echo "<div id='tables'>";
                if ($totalPoints <= $cutOffPoints) {
                    echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
        
                    // Insert data into the 'allocation' table
                    $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                    $insertStmt = $conn->prepare($insertQuery);
                    $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
                
                    if ($insertStmt->execute()) {
                        // Data inserted successfully, now retrieve and display it
                        $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                        $selectStmt = $conn->prepare($selectQuery);
                        $selectStmt->bind_param("s", $useremail);
                        $selectStmt->execute();
                        $selectResult = $selectStmt->get_result();
                
                        if ($selectResult->num_rows > 0) {
                            echo '<div id="message-content">ADMISSION DETAILS</div>';
                            echo '<div id="table">';
                            echo '<table>';
                            echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
                
                            while ($row = $selectResult->fetch_assoc()) {
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
                        }
                        $selectStmt->close();
                    } else {
                        echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                    }
                
                    $insertStmt->close();
                } else {
                    echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
                }
                
            echo "</div>";
            // Close the database connection
            $stmt->close();
            }
        }
            
        else if($school1||$school2 == 'MINAKI' && $comb2 == 'PGM') {
            // Connect to the database (assuming you have a $conn variable)
            // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
            $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,`Geo`,`B_Math` FROM `art_results` WHERE `CNo` = ?
                      UNION ALL
                      SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,`Geo`, `B_Math` FROM `business_results` WHERE `CNo` = ?
                      UNION ALL
                      SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Geo`, `B_Math` FROM `science_results` WHERE `CNo` = ?";
            $stmt = $conn->prepare($query);
            // Assuming you have stored the student's ID in a session variable
            $stmt->bind_param("sss", $studentid, $studentid, $studentid);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result->num_rows > 0) {
                // Initialize total points
                $totalPoints = 0;
        
                // Fetch the specific column data and table name
                $foundData = $result->fetch_assoc();
        
                // Calculate total points based on the grades (B=2, C=3)
                $subjects = ['Physics', 'Geo', 'B_Math'];
                foreach ($subjects as $subject) {
                    if (isset($foundData[$subject])) {
                        $grade = $foundData[$subject];
                        if ($grade == 'B') {
                            $totalPoints += 2;
                        } elseif ($grade == 'C') {
                            $totalPoints += 3;
                        }
                        elseif ($grade == 'D') {
                            $totalPoints += 4;
                        }
                        elseif ($grade == 'A') {
                            $totalPoints += 1;
                        }
                    }
                }
               
                // Check if the student's total points meet the admission criteria
                $cutOffPoints = 8; // Adjust this value as needed
                echo $totalPoints;
                echo "<div id='tables'>";
                if ($totalPoints <= $cutOffPoints) {
                    echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
        
                    // Insert data into the 'allocation' table
                    $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                    $insertStmt = $conn->prepare($insertQuery);
                    $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
                
                    if ($insertStmt->execute()) {
                        // Data inserted successfully, now retrieve and display it
                        $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                        $selectStmt = $conn->prepare($selectQuery);
                        $selectStmt->bind_param("s", $useremail);
                        $selectStmt->execute();
                        $selectResult = $selectStmt->get_result();
                
                        if ($selectResult->num_rows > 0) {
                            echo '<div id="message-content">ADMISSION DETAILS</div>';
                            echo '<div id="table">';
                            echo '<table>';
                            echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
                
                            while ($row = $selectResult->fetch_assoc()) {
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
                        }
                        $selectStmt->close();
                    } else {
                        echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                    }
                
                    $insertStmt->close();
                } else {
                    echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
                }
                
            echo "</div>";
            // Close the database connection
            $stmt->close();
            }
        }
            
        else if($school1||$school2 == 'MINAKI' && $comb3 == 'HKL') {
            // Connect to the database (assuming you have a $conn variable)
            // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
            $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Kisw` FROM `art_results` WHERE `CNo` = ?
                      UNION ALL
                      SELECT 'business_results' AS 'table_name', `CNo`, `Hist`,`Kisw`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
                      UNION ALL
                      SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Kisw`,NULL AS `Lit_engl` FROM `science_results` WHERE `CNo` = ?";
            $stmt = $conn->prepare($query);
            // Assuming you have stored the student's ID in a session variable
            $stmt->bind_param("sss", $studentid, $studentid, $studentid);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result->num_rows > 0) {
                // Initialize total points
                $totalPoints = 0;
        
                // Fetch the specific column data and table name
                $foundData = $result->fetch_assoc();
        
                // Calculate total points based on the grades (B=2, C=3)
                $subjects = ['Hist', 'Geo', 'Kisw'];
                foreach ($subjects as $subject) {
                    if (isset($foundData[$subject])) {
                        $grade = $foundData[$subject];
                        if ($grade == 'B') {
                            $totalPoints += 2;
                        } elseif ($grade == 'C') {
                            $totalPoints += 3;
                        }
                        elseif ($grade == 'D') {
                            $totalPoints += 4;
                        }
                        elseif ($grade == 'A') {
                            $totalPoints += 1;
                        }
                    }
                }
                // Check if the student's total points meet the admission criteria
                $cutOffPoints = 8; // Adjust this value as needed
                echo $totalPoints;
                echo "<div id='tables'>";
                if ($totalPoints <= $cutOffPoints) {
                    echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
        
                    // Insert data into the 'allocation' table
                    $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                    $insertStmt = $conn->prepare($insertQuery);
                    $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
                
                    if ($insertStmt->execute()) {
                        // Data inserted successfully, now retrieve and display it
                        $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                        $selectStmt = $conn->prepare($selectQuery);
                        $selectStmt->bind_param("s", $useremail);
                        $selectStmt->execute();
                        $selectResult = $selectStmt->get_result();
                
                        if ($selectResult->num_rows > 0) {
                            echo '<div id="message-content">ADMISSION DETAILS</div>';
                            echo '<div id="table">';
                            echo '<table>';
                            echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
                
                            while ($row = $selectResult->fetch_assoc()) {
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
                        }
                        $selectStmt->close();
                    } else {
                        echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                    }
                
                    $insertStmt->close();
                } else {
                    echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
                }
                
            echo "</div>";
            // Close the database connection
            $stmt->close();
            }
        }
            else if($school1||$school2 == 'MINAKI' && $comb3== 'HGL') {
                // Connect to the database (assuming you have a $conn variable)
                // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
                $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
                          UNION ALL
                          SELECT 'business_results' AS 'table_name', `CNo`, `Hist`,`Geo`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
                          UNION ALL
                          SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS `Lit_engl` FROM `science_results` WHERE `CNo` = ?";
                $stmt = $conn->prepare($query);
                // Assuming you have stored the student's ID in a session variable
                $stmt->bind_param("sss", $studentid, $studentid, $studentid);
                $stmt->execute();
                $result = $stmt->get_result();
            
                if ($result->num_rows > 0) {
                    // Initialize total points
                    $totalPoints = 0;
            
                    // Fetch the specific column data and table name
                    $foundData = $result->fetch_assoc();
            
                    // Calculate total points based on the grades (B=2, C=3)
                    $subjects = ['Hist', 'Geo', 'Lit_engl'];
                    foreach ($subjects as $subject) {
                        if (isset($foundData[$subject])) {
                            $grade = $foundData[$subject];
                            if ($grade == 'B') {
                                $totalPoints += 2;
                            } elseif ($grade == 'C') {
                                $totalPoints += 3;
                            }
                            elseif ($grade == 'D') {
                                $totalPoints += 4;
                            }
                            elseif ($grade == 'A') {
                                $totalPoints += 1;
                            }
                        }
                    }
                   
                    // Check if the student's total points meet the admission criteria
                    $cutOffPoints = 8; // Adjust this value as needed
                    echo $totalPoints;
                    echo "<div id='tables'>";
                    if ($totalPoints <= $cutOffPoints) {
                       
                        echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>Candidate Number</th><th>School</th><th>Combination</th></tr>';
                        echo '<tr><td>' . $studentid . '</td><td>' . $school1 . '</td><td>' . $comb1 . '</td></tr>';
                        echo '</table>';
                        echo '</div>';
                    } else {
                        echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
                    }
                } else {
                    echo '<div id="message-content">Student results not found in the database.</div>';
                }
           echo "</div>";
            // Close the database connection
            $stmt->close();
        }
           
            else if($school1||$school2 == 'MINAKI' && $comb3 == 'PGM') {
                // Connect to the database (assuming you have a $conn variable)
                // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
                $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,`Geo`,`B_Math` FROM `art_results` WHERE `CNo` = ?
                          UNION ALL
                          SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,`Geo`, `B_Math` FROM `business_results` WHERE `CNo` = ?
                          UNION ALL
                          SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Geo`, `B_Math` FROM `science_results` WHERE `CNo` = ?";
                $stmt = $conn->prepare($query);
                // Assuming you have stored the student's ID in a session variable
                $stmt->bind_param("sss", $studentid, $studentid, $studentid);
                $stmt->execute();
                $result = $stmt->get_result();
            
                if ($result->num_rows > 0) {
                    // Initialize total points
                    $totalPoints = 0;
            
                    // Fetch the specific column data and table name
                    $foundData = $result->fetch_assoc();
            
                    // Calculate total points based on the grades (B=2, C=3)
                    $subjects = ['Physics', 'Geo', 'B_Math'];
                    foreach ($subjects as $subject) {
                        if (isset($foundData[$subject])) {
                            $grade = $foundData[$subject];
                            if ($grade == 'B') {
                                $totalPoints += 2;
                            } elseif ($grade == 'C') {
                                $totalPoints += 3;
                            }
                            elseif ($grade == 'D') {
                                $totalPoints += 4;
                            }
                            elseif ($grade == 'A') {
                                $totalPoints += 1;
                            }
                        }
                    }
                   
                    // Check if the student's total points meet the admission criteria
                    $cutOffPoints = 8; // Adjust this value as needed
                    echo $totalPoints;
                    echo "<div id='tables'>";
                    if ($totalPoints <= $cutOffPoints) {
                        echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
            
                        // Insert data into the 'allocation' table
                        $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                        $insertStmt = $conn->prepare($insertQuery);
                        $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
                    
                        if ($insertStmt->execute()) {
                            // Data inserted successfully, now retrieve and display it
                            $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                            $selectStmt = $conn->prepare($selectQuery);
                            $selectStmt->bind_param("s", $useremail);
                            $selectStmt->execute();
                            $selectResult = $selectStmt->get_result();
                    
                            if ($selectResult->num_rows > 0) {
                                echo '<div id="message-content">ADMISSION DETAILS</div>';
                                echo '<div id="table">';
                                echo '<table>';
                                echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
                    
                                while ($row = $selectResult->fetch_assoc()) {
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
                            }
                            $selectStmt->close();
                        } else {
                            echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                        }
                    
                        $insertStmt->close();
                    } else {
                        echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
                    }
                    
                echo "<div>";
                // Close the database connection
                $stmt->close();
                }
            }
               
    else if($school1||$school2 == 'RUVU HIGH SCHOOL' && $comb1 == 'HGL') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, `Hist`,`Geo`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS `Lit_engl` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Hist', 'Geo', 'Lit_engl'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 9; // Adjust this value as needed
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">Inserted Data:</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
            }
            
        echo "</div>";
        // Close the database connection
        $stmt->close();
        }
    }
        else if($school1||$school2 == 'MINAKI' && $comb1 == 'HGL') {
            // Connect to the database (assuming you have a $conn variable)
            // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
            $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
                      UNION ALL
                      SELECT 'business_results' AS 'table_name', `CNo`, `Hist`,`Geo`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
                      UNION ALL
                      SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS `Lit_engl` FROM `science_results` WHERE `CNo` = ?";
            $stmt = $conn->prepare($query);
            // Assuming you have stored the student's ID in a session variable
            $stmt->bind_param("sss", $studentid, $studentid, $studentid);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result->num_rows > 0) {
                // Initialize total points
                $totalPoints = 0;
        
                // Fetch the specific column data and table name
                $foundData = $result->fetch_assoc();
        
                // Calculate total points based on the grades (B=2, C=3)
                $subjects = ['Hist', 'Geo', 'Lit_engl'];
                foreach ($subjects as $subject) {
                    if (isset($foundData[$subject])) {
                        $grade = $foundData[$subject];
                        if ($grade == 'B') {
                            $totalPoints += 2;
                        } elseif ($grade == 'C') {
                            $totalPoints += 3;
                        }
                        elseif ($grade == 'D') {
                            $totalPoints += 4;
                        }
                        elseif ($grade == 'A') {
                            $totalPoints += 1;
                        }
                    }
                }
               
                // Check if the student's total points meet the admission criteria
                $cutOffPoints = 8; // Adjust this value as needed
                echo $totalPoints;
                echo "<div id='tables'>";
                if ($totalPoints <= $cutOffPoints) {
                    echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
        
                    // Insert data into the 'allocation' table
                    $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                    $insertStmt = $conn->prepare($insertQuery);
                    $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
                
                    if ($insertStmt->execute()) {
                        // Data inserted successfully, now retrieve and display it
                        $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                        $selectStmt = $conn->prepare($selectQuery);
                        $selectStmt->bind_param("s", $useremail);
                        $selectStmt->execute();
                        $selectResult = $selectStmt->get_result();
                
                        if ($selectResult->num_rows > 0) {
                            echo '<div id="message-content">Inserted Data:</div>';
                            echo '<div id="table">';
                            echo '<table>';
                            echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
                
                            while ($row = $selectResult->fetch_assoc()) {
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
                        }
                        $selectStmt->close();
                    } else {
                        echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                    }
                
                    $insertStmt->close();
                } else {
                    echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
                }
            }
        echo "</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1 ||$school2== 'RUVU HIGH SCHOOL' && $comb1 == 'PCB') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`Bios` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,NULL AS`Chem`, `Bios` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`Bios` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Chem', 'Bios'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 9; // Adjust this value as needed
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
            }
            
        echo "</div>";
        // Close the database connection
        $stmt->close();
        }
    }
        
    else if($school1||$school2 == 'RUVU HIGH SCHOOL' && $comb1 == 'CBG') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Geo`, `Bios` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Chem', 'Geo', 'Bios'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 9; // Adjust this value as needed
            echo "<div id='tables'>";
             if($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
            }
            
        echo "</div>";
        // Close the database connection
        $stmt->close();
        }
    }
        
    else if($school1||$school2 == 'RUVU HIGH SCHOOL' && $comb2 == 'HGL') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, `Hist`,`Geo`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS `Lit_engl` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Hist', 'Geo', 'Lit_engl'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 9; // Adjust this value as needed
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
            }
            
        echo "</div>";
        // Close the database connection
        $stmt->close();
        }
    }
       
    else if($school1||$school2 == 'RUVU HIGH SCHOOL' && $comb2 == 'PCB') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`Bios` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,NULL AS`Chem`, `Bios` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`Bios` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Chem', 'Bios'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 9; // Adjust this value as needed
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
            }
            
        }
        // Close the database connection
        $stmt->close();
    }
    else if($school1||$school2 == 'RUVU HIGH SCHOOL' && $comb2 == 'CBG') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Geo`, `Bios` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Chem', 'Geo', 'Bios'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 9; // Adjust this value as needed
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
            }
        }
        echo "</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1||$school2 == 'RUVU HIGH SCHOOL' && $comb3 == 'HGL') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, `Hist`,`Geo`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS `Lit_engl` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Hist', 'Geo', 'Lit_engl'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 9; // Adjust this value as needed
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
            }
        }
        echo"</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1 == 'RUVU HIGH SCHOOL' && $comb3 == 'PCB') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`Bios` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,NULL AS`Chem`, `Bios` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`Bios` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Chem', 'Bios'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 9; // Adjust this value as needed
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
            }
        }
        echo "</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1 ||$school2== 'RUVU HIGH SCHOOL' && $comb3 == 'CBG') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Bios`, `Geo` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Chem', 'Bios', 'Geo'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 9; // Adjust this value as needed
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
            }
        }
        echo "</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1||$school2 == 'KIBAHA HIGH SCHOOL' && $comb1 == 'PCM') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`,NULL AS `Physics`,NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`, `B_Math` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Chem', 'B_Math'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 7; // Adjust this value as needed
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
            }
        }
       echo "</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1||$school2 == 'KIBAHA HIGH SCHOOL' && $comb1 == 'PCB') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`Bios` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,NULL AS`Chem`, `Bios` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`Bios` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Chem', 'Bios'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 7; // Adjust this value as needed
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
            }
        }
        echo "</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1||$school2 == 'KIBAHA HIGH SCHOOL' && $comb1 == 'PGM') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,`Geo`,`B_Math` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,`Geo`, `B_Math` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Geo`, `B_Math` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Geo', 'B_Math'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 7; // Adjust this value as needed
            echo $totalPoints;
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
            }
        }
      echo "</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1 ||$school2== 'KIBAHA HIGH SCHOOL' && $comb2 == 'PCM') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`,NULL AS `Physics`,NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`, `B_Math` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Chem', 'B_Math'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 7; // Adjust this value as needed
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
            }
        }
        echo "</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1||$school2 == 'KIBAHA HIGH SCHOOL' && $comb2 == 'PCB') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`Bios` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,NULL AS`Chem`, `Bios` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`Bios` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Chem', 'Bios'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 7; // Adjust this value as needed
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
            }
        }
        echo "</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1||$school2 == 'KIBAHA HIGH SCHOOL' && $comb2 == 'PGM') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,`Geo`,`B_Math` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,`Geo`, `B_Math` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Geo`, `B_Math` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Geo', 'B_Math'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 7; // Adjust this value as needed
            echo $totalPoints;
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
            }
        }
        echo "</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1||$school2 == 'KIBAHA HIGH SCHOOL' && $comb3 == 'PCM') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`,NULL AS `Physics`,NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`, `B_Math` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Chem', 'B_Math'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 7; // Adjust this value as needed
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb3 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
            }
        }
        echo "</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1||$school2 == 'KIBAHA HIGH SCHOOL' && $comb3 == 'PCB') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`Bios` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,NULL AS`Chem`, `Bios` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`Bios` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Chem', 'Bios'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           echo $totalPoints;
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 5; // Adjust this value as needed
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb3 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
            }
        }
        echo "</div>";
        // Close the database connection
        $stmt->close();
    }
    else if($school1||$school2 == 'KIBAHA HIGH SCHOOL' && $comb3 == 'PGM') {
        // Connect to the database (assuming you have a $conn variable)
        // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
        $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,`Geo`,`B_Math` FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,`Geo`, `B_Math` FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Geo`, `B_Math` FROM `science_results` WHERE `CNo` = ?";
        $stmt = $conn->prepare($query);
        // Assuming you have stored the student's ID in a session variable
        $stmt->bind_param("sss", $studentid, $studentid, $studentid);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Initialize total points
            $totalPoints = 0;
    
            // Fetch the specific column data and table name
            $foundData = $result->fetch_assoc();
    
            // Calculate total points based on the grades (B=2, C=3)
            $subjects = ['Physics', 'Geo', 'B_Math'];
            foreach ($subjects as $subject) {
                if (isset($foundData[$subject])) {
                    $grade = $foundData[$subject];
                    if ($grade == 'B') {
                        $totalPoints += 2;
                    } elseif ($grade == 'C') {
                        $totalPoints += 3;
                    }
                    elseif ($grade == 'D') {
                        $totalPoints += 4;
                    }
                    elseif ($grade == 'A') {
                        $totalPoints += 1;
                    }
                }
            }
           
            // Check if the student's total points meet the admission criteria
            $cutOffPoints = 7; // Adjust this value as needed
            echo $totalPoints;
            echo "<div id='tables'>";
            if ($totalPoints <= $cutOffPoints) {
                echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb3 . '.</div>';
    
                // Insert data into the 'allocation' table
                $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
            
                if ($insertStmt->execute()) {
                    // Data inserted successfully, now retrieve and display it
                    $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                    $selectStmt = $conn->prepare($selectQuery);
                    $selectStmt->bind_param("s", $useremail);
                    $selectStmt->execute();
                    $selectResult = $selectStmt->get_result();
            
                    if ($selectResult->num_rows > 0) {
                        echo '<div id="message-content">ADMISSION DETAILS</div>';
                        echo '<div id="table">';
                        echo '<table>';
                        echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
            
                        while ($row = $selectResult->fetch_assoc()) {
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
                    }
                    $selectStmt->close();
                } else {
                    echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
                }
            
                $insertStmt->close();
            } else {
                echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
            }
        
      echo "</div>";
        // Close the database connection
        $stmt->close();
        
    }
}
else if($school1 ||$school2== 'MZUMBE HIGH SCHOOL' && $comb1 == 'PCB') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`Bios` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,NULL AS`Chem`, `Bios` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`Bios` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'Bios'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MZUMBE HIGH SCHOOL' && $comb1 == 'EGM') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`B_keeping`,NULL AS`Geo`,`B_Math` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`B_keeping`,`Geo`, `B_Math` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, NULL AS`B_keeping`, `Geo`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['B_Keeping', 'Geo', 'B_Math'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1 ||$school2== 'MZUMBE HIGH SCHOOL' && $comb1 == 'CBG') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,NULL AS`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Bios`,`Geo` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Chem', 'Bios', 'Geo'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MZUMBE HIGH SCHOOL' && $comb2 == 'PCB') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`Bios` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,NULL AS`Chem`, `Bios` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`Bios` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'Bios'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2. '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MZUMBE HIGH SCHOOL' && $comb2 == 'EGM') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`B_keeping`,NULL AS`Geo`,`B_Math` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`B_keeping`,`Geo`, `B_Math` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, NULL AS`B_keeping`, `Geo`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['B_Keeping', 'Geo', 'B_Math'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MZUMBE HIGH SCHOOL' && $comb2 == 'CBG') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,NULL AS`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Bios`,`Geo` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Chem', 'Bios', 'Geo'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MZUMBE HIGH SCHOOL' && $comb3 == 'PCB') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`Bios` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,NULL AS`Chem`, `Bios` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`Bios` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'Bios'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb3 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb3, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MZUMBE HIGH SCHOOL' && $comb3 == 'EGM') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`B_keeping`,NULL AS`Geo`,`B_Math` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`B_keeping`,`Geo`, `B_Math` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, NULL AS`B_keeping`, `Geo`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['B_Keeping', 'Geo', 'B_Math'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb3 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MZUMBE HIGH SCHOOL' && $comb3 == 'CBG') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,NULL AS`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Bios`,`Geo` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Chem', 'Bios', 'Geo'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb3 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb3, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MOROGORO SECONDARY' && $comb1 == 'PCB') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`Bios` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,NULL AS`Chem`, `Bios` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`Bios` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'Bios'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MOROGORO SECONDARY' && $comb1 == 'HGE') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,NULL AS`B_Keeping` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`Hist`,`Geo`, `B_Keeping` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS`B_Keeping` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Geo', 'B_Keeping'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MOROGORO SECONDARY' && $comb1 == 'HKL') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Kisw`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`Hist`,`Kisw`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Kisw`,NULL AS`Lit_engl` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Kisw', 'Lit_engl'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MOROGORO SECONDARY' && $comb2 == 'PCB') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`Bios` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,NULL AS`Chem`, `Bios` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`Bios` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'Bios'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MOROGORO SECONDARY' && $comb2 == 'HGE') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,NULL AS`B_Keeping` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`Hist`,`Geo`, `B_Keeping` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS`B_Keeping` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Geo', 'B_Keeping'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MOROGORO SECONDARY' && $comb2 == 'HKL') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Kisw`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`Hist`,`Kisw`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Kisw`,NULL AS`Lit_engl` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Kisw', 'Lit_engl'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1 == 'MOROGORO SECONDARY' && $comb3 == 'PCB') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`Bios` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Physics`,NULL AS`Chem`, `Bios` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`Bios` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'Bios'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb3 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb3, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MOROGORO SECONDARY' && $comb3 == 'HGE') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,NULL AS`B_Keeping` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`Hist`,`Geo`, `B_Keeping` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS`B_Keeping` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Geo', 'B_Keeping'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb3 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb3, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1 ||$school2== 'MOROGORO SECONDARY' && $comb3 == 'HKL') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Kisw`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`Hist`,`Kisw`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Kisw`,NULL AS`Lit_engl` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Kisw', 'Lit_engl'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints < $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb3 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb3, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'DAKAWA HIGH SCHOOL' && $comb1 == 'HGK'){
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Kisw` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, `Hist`,`Geo`, `Kisw` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,`Kisw` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Geo', 'Kisw'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'DAKAWA HIGH SCHOOL' && $comb1 == 'PCM') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'B_Math'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints < $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'DAKAWA HIGH SCHOOL' && $comb1 == 'CBG') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Bios`,`Geo` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Chem', 'Bios', 'Geo'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'DAKAWA HIGH SCHOOL' && $comb2 == 'HGK'){
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Kisw` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, `Hist`,`Geo`, `Kisw` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,`Kisw` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Geo', 'Kisw'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'DAKAWA HIGH SCHOOL' && $comb2 == 'PCM') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'B_Math'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'DAKAWA HIGH SCHOOL' && $comb2 == 'CBG') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Bios`,`Geo` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Chem', 'Bios', 'Geo'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'DAKAWA HIGH SCHOOL' && $comb3 == 'HGK'){
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Kisw` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, `Hist`,`Geo`, `Kisw` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,`Kisw` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Geo', 'Kisw'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb3 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb3, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'DAKAWA HIGH SCHOOL' && $comb3 == 'PCM') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'B_Math'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb3 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb3, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'DAKAWA HIGH SCHOOL' && $comb3 == 'CBG') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Bios`,`Geo` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'Bios'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb3 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb3, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb3 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MIRAMBO HIGH SCHOOL' && $comb1 == 'CBG'){
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Bios`,`Geo` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Chem', 'Bios', 'Geo'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MIRAMBO HIGH SCHOOL' && $comb1 == 'PCM') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'B_Math'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints < $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MIRAMBO HIGH SCHOOL' && $comb1 == 'HGL') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`Hist`,`Geo`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS`Lit_engl` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Geo', 'Lit_engl'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MIRAMBO HIGH SCHOOL' && $comb2 == 'CBG'){
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Bios`,`Geo` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Chem', 'Bios', 'Geo'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MIRAMBO HIGH SCHOOL' && $comb2 == 'PCM') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'B_Math'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'MIRAMBO HIGH SCHOOL' && $comb2 == 'HGL') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`Hist`,`Geo`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS`Lit_engl` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Geo', 'Lit_engl'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 8; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'TABORA GIRLS HIGH SCHOOL' && $comb1 == 'CBG'){
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Bios`,`Geo` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Chem', 'Bios', 'Geo'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'TABORA GIRLS HIGH SCHOOL' && $comb1 == 'PCM') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'B_Math'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'TABORA GIRLS HIGH SCHOOL' && $comb1 == 'HGL') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`Hist`,`Geo`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS`Lit_engl` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Geo', 'Lit_engl'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'TABORA GIRLS HIGH SCHOOL' && $comb2 == 'CBG'){
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Bios`,`Geo` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Chem', 'Bios', 'Geo'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'TABORA GIRLS HIGH SCHOOL' && $comb2 == 'PCM') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'B_Math'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'TABORA GIRLS HIGH SCHOOL' && $comb2 == 'HGL') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`Hist`,`Geo`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS`Lit_engl` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Geo', 'Lit_engl'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 7; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'KAZIMA HIGH SCHOOL' && $comb1 == 'CBG'){
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Bios`,`Geo` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Chem', 'Bios', 'Geo'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'KAZIMA HIGH SCHOOL' && $comb1 == 'PCM') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'B_Math'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'KAZIMA HIGH SCHOOL' && $comb1 == 'HGL') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`Hist`,`Geo`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS`Lit_engl` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Geo', 'Lit_engl'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb1 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb1, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb1 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'KAZIMA HIGH SCHOOL' && $comb2 == 'CBG'){
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Chem`,`Bios`,`Geo` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`, NULL AS`Chem`,`Bios`, `Geo` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Chem`, `Bios`,`Geo` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Chem', 'Bios', 'Geo'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'KAZIMA HIGH SCHOOL' && $comb2 == 'PCM') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`,`B_Math` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,NULL AS`Physics`,NULL AS`Chem`, `B_Math` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Physics`, `Chem`,`B_Math` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Physics', 'Chem', 'B_Math'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($school1||$school2 == 'KAZIMA HIGH SCHOOL' && $comb2 == 'HGL') {
    // Connect to the database (assuming you have a $conn variable)
    // Query to check if the student's ID exists in any of the three tables (art_results, business_results, science_results)
    $query = "SELECT 'art_results' AS 'table_name', `CNo`,`Hist`,`Geo`,`Lit_engl` FROM `art_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'business_results' AS 'table_name', `CNo`,`Hist`,`Geo`,NULL AS `Lit_engl` FROM `business_results` WHERE `CNo` = ?
              UNION ALL
              SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Geo`,NULL AS`Lit_engl` FROM `science_results` WHERE `CNo` = ?";
    $stmt = $conn->prepare($query);
    // Assuming you have stored the student's ID in a session variable
    $stmt->bind_param("sss", $studentid, $studentid, $studentid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Initialize total points
        $totalPoints = 0;

        // Fetch the specific column data and table name
        $foundData = $result->fetch_assoc();

        // Calculate total points based on the grades (B=2, C=3)
        $subjects = ['Hist', 'Geo', 'Lit_engl'];
        foreach ($subjects as $subject) {
            if (isset($foundData[$subject])) {
                $grade = $foundData[$subject];
                if ($grade == 'B') {
                    $totalPoints += 2;
                } elseif ($grade == 'C') {
                    $totalPoints += 3;
                }
                elseif ($grade == 'D') {
                    $totalPoints += 4;
                }
                elseif ($grade == 'A') {
                    $totalPoints += 1;
                }
            }
        }
       echo $totalPoints;
        // Check if the student's total points meet the admission criteria
        $cutOffPoints = 9; // Adjust this value as needed
        echo "<div id='tables'>";
        if ($totalPoints <= $cutOffPoints) {
            echo '<div id="message-content">Dear candidate ' . $studentid . ' Congratulations! You are qualified for ' . $school1 . ' for the combination of ' . $comb2 . '.</div>';

            // Insert data into the 'allocation' table
            $insertQuery = "INSERT INTO allocation (schoolName, combination, fullName, region, CNo, email) VALUES (?,?,?,?,?,?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssssss", $school1, $comb2, $name, $mkoa, $studentid, $useremail);
        
            if ($insertStmt->execute()) {
                // Data inserted successfully, now retrieve and display it
                $selectQuery = "SELECT schoolName, combination, fullName, region, CNo, email FROM allocation WHERE email = ?";
                $selectStmt = $conn->prepare($selectQuery);
                $selectStmt->bind_param("s", $useremail);
                $selectStmt->execute();
                $selectResult = $selectStmt->get_result();
        
                if ($selectResult->num_rows > 0) {
                    echo '<div id="message-content">ADMISSION DETAILS</div>';
                    echo '<div id="table">';
                    echo '<table>';
                    echo '<tr><th>School Name</th><th>Combination</th><th>Full Name</th><th>Region</th><th>Candidate Number</th><th>Email</th></tr>';
        
                    while ($row = $selectResult->fetch_assoc()) {
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
                }
                $selectStmt->close();
            } else {
                echo '<div id="message-content">Error: Failed to insert data into allocation.</div>';
            }
        
            $insertStmt->close();
        } else {
            echo '<div id="message-content">Dear candidate ' . $studentid . '  Sorry, you do not meet the admission criteria for ' . $school1 . ' for the combination of ' . $comb2 . '. </div>';
        }
    }
    echo "</div>";
    // Close the database connection
    $stmt->close();
}
else if($comb1==" "||$comb2==" "||$comb3==" "){
    echo "Invalid Schoolname or Combination";
}
else{
    echo "invalid";
}
    ?>
    </div>
</body>
</html>