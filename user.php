<!-- user.php -->
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="selform.css">
    <title>User Information</title>
    <style>
        /* Rest of your existing CSS styles */
        table {
            border-collapse: collapse;
            width: 100%;
            display: flex;
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

        h4 {
            font-family: courier;
            text-align: justify;
            text-transform: uppercase;
        }
        /* New CSS styles for layout */
        #content {
            display: flex;
            flex-direction: row;
            background-color: whitesmoke;
            font-family: serif;
            height: 32em;
            margin-left: 15px;
        }

        #side {
            width:fit-content; /* Set the width of the side navigation bar */
            background-color: #f2f2f2;
            padding: 10px;
            text-align: center;
            background-color:rgba(223, 223, 225,.5);
            height: 100vh;
        }

        #table-container {
             flex:1;/* Let the table container take remaining width */
            padding: 10px; 
            height: 26.8em;
        }
        #set2{
            width: max-content;
            padding:10px;
            margin-left:10px;
        }
      #choose1{
        margin-bottom:10px;
        width:25em;
        height:2em;
      }
      #choose2{
        width:25em;
        height:2em;
      }
      #next2{
        background-color: #0c356A;
    color: white;
    font-family:courier;
    padding: 10px;
    border-radius: 0.4em;
    border:none ;
      }
      #content-container{
        margin-top: 30px;
      }
      #ses{
        font-family: courier;
        font-weight: bolder;
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
<div id="content">
    <div id="side">
        <!-- Your side navigation bar content -->
        <a href="selform.php"><img src="IMAGES/dashboard.png" alt="" id="icon1" title="dashboard"></a><br>
        <a href="form.php"><img src="IMAGES/google-forms.png" alt="" id="icon2" title="selform"></a><br>
        <a href=""><img src="IMAGES/allocation.png" alt="" id="icon3" title="myAllocation"><br>
        <a href="change.php"><img src="IMAGES/password.png" alt="" id="icon4" title="change password"></a><br>
        <a href="login."><img src="IMAGES/power-off.png" alt="" id="icon5" title="Logout"></a>
    </div>
    <div id="table-container">
        <?php 
        session_start();
        include('conn.php');
        if (isset($_SESSION['foundData'])) {
            $foundData = $_SESSION['foundData'];

            // Determine the table based on the 'table_name' value
            $table = $foundData['table_name'];

            // Start the HTML table
            echo '<table>';

            // Display the table name
            echo "<h4>RESULTS</h4>";

            // Display the specific columns based on the table
            switch ($table) {
                case 'art_results':
                    echo "<tr>";
                    echo "<th>CNo</th>";
                    echo "<th>School Name</th>";
                    echo "<th>Civ</th>";
                    echo "<th>Hist</th>";
                    echo "<th>Geo</th>";
                    echo "<th>Kisw</th>";
                    echo "<th>Engl</th>";
                    echo "<th>Bios</th>";
                    echo "<th>B Math</th>";
                    echo "<th>Lit</th>";
                    echo "<th>Div</th>";
                    echo "<th>Point</th>";
                    
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td>" . $foundData['CNo'] . "</td>";
                    echo "<td>" . $foundData['schoolname'] . "</td>";
                    echo "<td>" . $foundData['Civ'] . "</td>";
                    echo "<td>" . $foundData['Hist'] . "</td>";
                    echo "<td>" . $foundData['Geo'] . "</td>";
                    echo "<td>" . $foundData['Kisw'] . "</td>";
                    echo "<td>" . $foundData['Engl'] . "</td>";
                    echo "<td>" . $foundData['Bios'] . "</td>";
                    echo "<td>" . $foundData['B_Math'] . "</td>";
                    echo "<td>" . $foundData['Lit_engl'] . "</td>";
                    echo "<td>" . $foundData['division'] . "</td>";
                    echo "<td>" . $foundData['point'] . "</td>";
                    echo "</tr>";
                    break;
                case 'business_results':
                    echo "<tr>";
                    echo "<th>CNo</th>";
                    echo "<th>School Name</th>";
                    echo "<th>Civ</th>";
                    echo "<th>Hist</th>";
                    echo "<th>Geo</th>";
                    echo "<th>Kisw</th>";
                    echo "<th>Engl</th>";
                    echo "<th>Bios</th>";
                    echo "<th>B Math</th>";
                    echo "<th>B_Keeping</th>";
                    echo "<th>Div</th>";
                    echo "<th>Point</th>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td>" . $foundData['CNo'] . "</td>";
                    echo "<td>" . $foundData['schoolname'] . "</td>";
                    echo "<td>" . $foundData['Civ'] . "</td>";
                    echo "<td>" . $foundData['Hist'] . "</td>";
                    echo "<td>" . $foundData['Geo'] . "</td>";
                    echo "<td>" . $foundData['Kisw'] . "</td>";
                    echo "<td>" . $foundData['Engl'] . "</td>";
                    echo "<td>" . $foundData['Bios'] . "</td>";
                    echo "<td>" . $foundData['B_Math'] . "</td>";
                    echo "<td>" . $foundData['Kisw'] . "</td>";
                    echo "<td>" . $foundData['division'] . "</td>";
                    echo "<td>" . $foundData['point'] . "</td>";
                    echo "</tr>";
                    break;
                case 'science_results':
                    echo "<tr>";
                    echo "<th>CNo</th>";
                    echo "<th>School Name</th>";
                    echo "<th>Civ</th>";
                    echo "<th>Physics</th>";
                    echo "<th>Geo</th>";
                    echo "<th>Kisw</th>";
                    echo "<th>Engl</th>";
                    echo "<th>Bios</th>";
                    echo "<th>Chem</th>";
                    echo "<th>B Math</th>";
                    echo "<th>Div</th>";
                    echo "<th>Point</th>";
                    
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td>" . $foundData['CNo'] . "</td>";
                    echo "<td>" . $foundData['schoolname'] . "</td>";
                    echo "<td>" . $foundData['Civ'] . "</td>";
                    echo "<td>" . $foundData['Kisw'] . "</td>";
                    echo "<td>" . $foundData['Geo'] . "</td>";
                    echo "<td>" . $foundData['Kisw'] . "</td>";
                    echo "<td>" . $foundData['Engl'] . "</td>";
                    echo "<td>" . $foundData['Bios'] . "</td>";
                    echo "<td>" . $foundData['Hist'] . "</td>";
                    echo "<td>" . $foundData['B_Math'] . "</td>";
                    echo "<td>" . $foundData['division'] . "</td>";
                    echo "<td>" . $foundData['point'] . "</td>";
                    
                    echo "</tr>";
                    break;
                default:
                    echo "<tr><td colspan='11'>No specific result found for the candidate number.</td></tr>";
                    break;
            }

            // Close the HTML table
            echo "</table>";
        }
          ?>
           
          <?php
        
        
            // Found data not available
        
        

        // Check if Necta information is available in the session
        if (isset($_SESSION['nectaInfo'])) {
            $nectaInfo = $_SESSION['nectaInfo'];
            // Display the Necta information
            echo '<h4>Student Details</h4>';
            echo '<table>';
            echo '<tr><th>Gender</th><th>Candidate Number</th><th>Region</th><th>School Name</th></tr>';
            echo '<tr>';
            echo '<td>' . $nectaInfo['Gender'] . '</td>';
            echo '<td>' . $nectaInfo['CNo'] . '</td>';
            echo '<td>' . $nectaInfo['region'] . '</td>';
            echo '<td>' . $nectaInfo['schoolname'] . '</td>';
            echo '</tr>';
            echo '</table>';
        } else {
            echo 'No Necta information found.';
        }
        // Check if school information is available in the session
        if (isset($_SESSION['schoolInfo'])) {
            $schoolInfo = $_SESSION['schoolInfo'];

            // Display the school information
            echo '<h4>School Information</h4>';
            echo '<table>';
            echo '<tr><th>School REG NO</th><th>SchoolName</th><th>Region</th><th>Combinations Offered</th><th>Admission Criteria</th></tr>';

            foreach ($schoolInfo as $row) {
                echo '<tr>';
                echo '<td>' . $row["schoolID"] . '</td>';
                echo '<td>' . $row["sName"] . '</td>';
                echo '<td>' . $row["region"] . '</td>';
                echo '<td>' . $row["combinations_offered"] . '</td>';
                echo '<td>' . $row["admission_criteria"] . '</td>';
                echo '</tr>';
            }

            echo '</table>';
            ?>
    <div id="content-container">
   
    <fieldset id="set2">
    <legend id="ses">SESSION 2</legend>
    <p>School selection</p>
    <form method="post" action="action6.php" enctype="multipart/form-data">
        <?php
        $allowedRegions = ["TABORA", "ARUSHA", "MOROGORO", "DODOMA", "PWANI"];
        $studentId=$_SESSION['number'];
        $useremail=$_SESSION['email'];
        $region = $_SESSION['region'];
        $number=$_SESSION['number'];
        $name=$_SESSION['name'];
        if (in_array($region, $allowedRegions)) {
            $sql = "SELECT * FROM highschool WHERE region = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $region);
            $stmt->execute();
            $results = $stmt->get_result();
            $result = $results->fetch_all(MYSQLI_ASSOC);
            ?>
            <select name="School2" id="">
                <option value=""  disabled>CHOOSE FIRST CHOICE</option>
                <?php
                foreach ($result as $chosen) {
                    echo '<option name="" value="' . $chosen['sName'] . '">' . $chosen['sName'] . '</option>';
                }
                ?>
            </select>
            <?php
        } else {
        }
        ?>
        <?php
        $allowedRegions = ["TABORA", "ARUSHA", "MOROGORO", "DODOMA", "PWANI"];
        $region = $_SESSION['region'];
        if (in_array($region, $allowedRegions)) {
            $sql = "SELECT * FROM highschool WHERE region = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $region);
            $stmt->execute();
            $results = $stmt->get_result();
            $result = $results->fetch_all(MYSQLI_ASSOC);
            ?>
            <select name="School1" id="">
                <option value=""  disabled>CHOOSE SECOND CHOICE</option>
                <?php
                foreach ($result as $chosen) {
                    echo '<option name="" value="' . $chosen['sName'] . '">' . $chosen['sName'] . '</option>';
                }
                ?>
            </select>
            <?php
        } else {
        }
        ?>
        <br><br>
        <p>Choose three combinations eg: PCM,PCB</p>
        <input type="text" placeholder="first choice" id="choose1" name="comb1" oninput="limitinput(this)">
        <input type="text" placeholder="second choice" id="choose2" name="comb2" oninput="limitinput(this)">
        <input type="text" placeholder="third choice" id="choose2" name="comb3" oninput="limitinput(this)"><br><br>
        <button type="submit" id="next2" name="next2">NEXT</button>
    </form>
</fieldset>
<script>
    function limitinput(inputField) {
        // Remove non-letter characters as the user types
        inputField.value = inputField.value.replace(/[^A-Za-z]/g, '');

        // Convert input to uppercase
        inputField.value = inputField.value.toUpperCase();

        if (inputField.value.length >= 3) {
            inputField.value = inputField.value.substr(0, 3); // Truncate input to 3 characters
        }
    }
</script>

</div>
    </div>
</div>
<?php
            
        } else {
            echo 'No school information found.';
        }
    ?>
    
</body>
</html>