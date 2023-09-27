<?php
session_start();
include('conn.php');

// Define the mapping of short form letters to subjects
$subjectMapping = [
    'HKL' => ['Hist', 'Kisw', 'Lit_engl'],
    'HGL' => ['Hist', 'Geo', 'Lit_engl'],
    'HGE' => ['Hist', 'Geo', 'Comm'],
    'CBG' => ['Chem', 'Bios', 'Geo'],
    'PCB' => ['Physics', 'Chem', 'Bios'],
    'PCM' => ['Physics', 'Chem', 'B_Math'],
    'EGM' => ['B_Keeping', 'Geo', 'B_Math'],
    // Add more mappings as needed
];

// Initialize an array to store the subjects
$chosenSubjects = [];

// Check if the form has been submitted and next2 is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["next2"])) {
    // Analyze the input for comb1, comb2, and comb3
    foreach (['comb1,comb2,comb3'] as $inputName) {
        if (isset($_POST[$inputName])) {
            $shortForm = strtoupper($_POST[$inputName]); // Convert to uppercase
            $subjects = $subjectMapping[$shortForm] ?? ['Unknown']; // Get the corresponding subjects or 'Unknown'
            $chosenSubjects = array_merge($chosenSubjects, $subjects);
        }
    }

    // Check if the input with name "tee" is set
    if (isset($_POST['tee'])) {
        $_SESSION['tee'] = $_POST['tee'];
        // Connect to the database (assuming you have a $conn variable)
        // Query to find the "tee" value in the three tables (art_results, business_results, science_results)
        $candidateNumber = $_POST['tee'];

        $query = "SELECT 'art_results' AS 'table_name', `CNo`, `Hist`, `Civ`, `Bios`'Geo' FROM `art_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'business_results' AS 'table_name', `CNo`, `Hist`, `Civ`, `Bios`'Geo' FROM `business_results` WHERE `CNo` = ?
                  UNION ALL
                  SELECT 'science_results' AS 'table_name', `CNo`, `Hist`, `Civ`, `Bios`'Chem' FROM `science_results` WHERE `CNo` = ?";

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
        } else {
            $_SESSION['foundData'] = null; // No data found
        }

        // Check if the user has selected a school and combination
        if (isset($_POST['school']) && isset($_POST['combination'])) {
            $chosenSchool = $_POST['school'];
            $chosenCombination = $_POST['combination'];

            // Query to retrieve the list of schools from the 'highschool' table
            $schoolsQuery = "SELECT DISTINCT `sName` FROM `highschool`";
            $schoolsResult = $conn->query($schoolsQuery);

            if ($schoolsResult) {
                $schoolsList = [];
                while ($row = $schoolsResult->fetch_assoc()) {
                    $schoolsList[] = $row['sName'];
                }

                if (in_array($chosenSchool, $schoolsList)) {
                    // The user's chosen school exists in the list of schools from the database

                    // Define qualification criteria for each school and combination (customize this based on your requirements)
                    $qualificationCriteria = [
                        'MINAKI' => [
                            'requiredSubjects' => ['Hist', 'Geo', 'Kisw'],
                            'minimumGrades' => [
                                'H' => 'B',
                                'G' => 'C',
                                'K' => 'B'
                            ]
                        ],
                        'KIBAHA HIGH SCHOOL' => [
                            'requiredSubjects' => ['P', 'C', 'M'],
                            'minimumGrades' => [
                                'P' => 'B',
                                'C' => 'C',
                                'M' => 'B'
                            ]
                        ],
                        // Define criteria for other schools and combinations
                    ];
                    // Check if the user's chosen school and combination exist in the qualification criteria
                    $criteriaKey = "$chosenSchool" . "_" . "$chosenCombination";
                    if (isset($qualificationCriteria[$criteriaKey])) {
                        $criteria = $qualificationCriteria[$criteriaKey];

                        // Check if the user has taken the required subjects
                        $missingSubjects = array_diff($criteria['requiredSubjects'], array_keys($_SESSION['foundData']));
                        if (empty($missingSubjects)) {
                            // All required subjects are taken, now check grades if needed
                            $failedSubjects = [];

                            foreach ($criteria['minimumGrades'] as $subject => $minGrade) {
                                if (!isset($_SESSION['foundData'][$subject])) {
                                    // Subject is missing
                                    $failedSubjects[] = "$subject (Not Taken)";
                                } elseif ($_SESSION['foundData'][$subject] < $minGrade) {
                                    // Subject grade is below the minimum
                                    $failedSubjects[] = "$subject (Grade: " . $_SESSION['foundData'][$subject] . ")";
                                }
                            }

                            if (empty($failedSubjects)) {
                                // User meets subject and grade requirements
                                echo "Congratulations! You are qualified for $chosenSchool in $chosenCombination.";
                            } else {
                                // User doesn't meet subject or grade requirements
                                echo "Sorry, you are not qualified for $chosenSchool in $chosenCombination. You have deficiencies in: " . implode(', ', $failedSubjects);
                            }
                        } else {
                            // User is missing required subjects
                            echo "Sorry, you are not qualified for $chosenSchool in $chosenCombination. You are missing subjects: " . implode(', ', $missingSubjects);
                        }
                    } else {
                        // School or combination not found in criteria
                        echo "Invalid school or combination selected.";
                    }
                } else {
                    // User's chosen school does not exist in the list of schools from the database
                    echo "Invalid school selected.";
                }
            } else {
                // Error in querying schools from the database
                echo "Error fetching schools from the database.";
            }
        }
    }
    // Redirect to do.php or perform other actions as needed
    // header("Location: do.php");
    // exit;
}  
?>
