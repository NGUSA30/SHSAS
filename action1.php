<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<style>
    #table {
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
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
</style>
<?php
include('conn.php');
session_start();

function validateCandidateNumber($candidateNumber) {
    // Define a regular expression pattern for the candidate number format
    $pattern = '/^S\d{4}-\d{4}-\d{4}$/';
    return preg_match($pattern, $candidateNumber);
}
if (isset($_POST['next1'])) {
    $region = $_POST['mkoa'];
    $candidateNumber = $_POST['number'];
    $_SESSION['mtani']=$_POST['number'];
    $fullName = $_POST['candidate'];
    $_SESSION['region']=$region;
    $_SESSION['number']=$candidateNumber;
    $_SESSION['email']=$_POST['useremail'];
    $_SESSION['name']=$_POST['candidate'];
    $checkStudentQuery = "SELECT * FROM students WHERE CNo = ?";
            $checkStudentStmt = $conn->prepare($checkStudentQuery);
            $checkStudentStmt->bind_param("s", $candidateNumber);
            $checkStudentStmt->execute();
            $studentResult = $checkStudentStmt->get_result();

            if ($studentResult->num_rows > 0) {
                // $candidateNumber exists in the students table
                // Add your code here to handle this case
                // You can fetch the student's details using $studentResult if needed
                // For example: $studentDetails = $studentResult->fetch_assoc();

        // Check if the candidate is already admitted
        $checkAdmissionQuery = "SELECT * FROM allocation WHERE CNo = ?";
        $checkAdmissionStmt = $conn->prepare($checkAdmissionQuery);
        $checkAdmissionStmt->bind_param("s", $candidateNumber);
        $checkAdmissionStmt->execute();
        $admissionResult = $checkAdmissionStmt->get_result();
    
        if ($admissionResult->num_rows > 0) {
                // Candidate is already admitted
        $_SESSION['admissionDetails'] = [];

            while ($row = $admissionResult->fetch_assoc()) {
                $_SESSION['admissionDetails'][] = $row;
        }
        header("Location: allocation.php");
        exit(); // Terminate script execution
    }
        else {
            // Candidate is not admitted; continue with the admission process
            // ... (rest of your code)
       
    

    // Validate the candidate number format
    if (!validateCandidateNumber($candidateNumber)) {
        echo '<p>Invalid candidate number format. Please use the format S0112-0023-2023.</p>';
    } elseif (empty($fullName)) {
        echo '<p>Please enter your name.</p>';
    } elseif (strlen($fullName) > 50) {
        echo '<p>You have reached the maximum characters for the full name.</p>';
    } elseif (preg_match('/\d/', $fullName)) {
        echo '<p>Name should not contain numbers.</p>';
    } else {
       
        }
        

        $allowedRegions = ['TABORA', 'ARUSHA', 'MOROGORO', 'DODOMA', 'PWANI'];

        // Check if the selected region is in the list of allowed regions
        if (in_array($region, $allowedRegions)) {
            // Construct a dynamic SQL query with a placeholder for the region
            $sql = "SELECT * FROM highschool WHERE region = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $region);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Fetch the school information and store it in an array
                $schoolInfo = array();
                while ($row = $result->fetch_assoc()) {
                    $schoolInfo[] = $row;
                }

                // Store the school information array in a session variable
                $_SESSION['schoolInfo'] = $schoolInfo;

                // Close the database statement and connection
                $stmt->close();

                // Continue with fetching Necta information
                if (isset($_POST['next1'])) {
                    $candidateNumber = $_POST['number'];
                    // Define the SQL query to fetch data from the NectaResult table
                    $nectaQuery = "SELECT `Gender`, `CNo`, `region`, `schoolname` FROM `NectaResult` WHERE `CNo` = ?";

                    // Prepare and execute the NectaResult query
                    $stmtNecta = $conn->prepare($nectaQuery);
                    $stmtNecta->bind_param("s", $candidateNumber);
                    $stmtNecta->execute();
                    $resultNecta = $stmtNecta->get_result();

                    if ($resultNecta->num_rows > 0) {
                        // Store the Necta information in a session variable
                        $_SESSION['nectaInfo'] = $resultNecta->fetch_assoc();

                        // Close the NectaResult statement
                        $stmtNecta->close();

                        // Check if the candidate number is present in any of the three tables
                        $query = "SELECT 'art_results' AS 'table_name', `CNo`, `schoolname`, `Civ`, `Hist`, `Geo`, `Kisw`, `Engl`, `Bios`, `B_Math`, `Lit_engl`, `division`, `point` FROM `art_results` WHERE `CNo` = ?
                                  UNION ALL
                                  SELECT 'business_results' AS 'table_name', `CNo`, `schoolname`, `Civ`, `Hist`, `Geo`, `Kisw`, `Engl`, `Bios`, `B_Math`, `Comm`, `division`, `point` FROM `business_results` WHERE `CNo` = ?
                                  UNION ALL
                                  SELECT 'science_results' AS 'table_name', `CNo`, `schoolname`, `Civ`, `Hist`, `Geo`, `Kisw`, `Engl`, `Bios`, `Chem`, `B_Math`, `division`, `point` FROM `science_results` WHERE `CNo` = ?";

                        $stmtQuery = $conn->prepare($query);
                        $stmtQuery->bind_param("sss", $candidateNumber, $candidateNumber, $candidateNumber);
                        $stmtQuery->execute();
                        $resultQuery = $stmtQuery->get_result();

                        if ($resultQuery->num_rows > 0) {
                            // Fetch the specific column data and table name
                            $foundData = $resultQuery->fetch_assoc();

                            // Store the found data and table name in session variables
                            $_SESSION['foundData'] = $foundData;

                            // Close the query statement
                            $stmtQuery->close();
                        }

                        // Redirect to user.php
                        header("Location: user.php");
                        exit;
                    } else {
                        echo '<p>No results found for the candidate number.</p>';
                    }
                }
            }
        }
        $checkAdmissionStmt->close();
    }
}
else{
    $loginError = "Invalid username or password";
}
}
?>
</body>
</html>