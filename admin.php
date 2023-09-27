<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="mchongo.css">
    <link rel="stylesheet" href="selform.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selform</title>
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
    <a href="" ><img src="IMAGES/allocation.png" alt="" id="icon3" title="myAllocation"><br>
     <a href="change.php"><img src="IMAGES/password.png" alt="" id="icon4" title="change password"></a><br>
     <a href="login.php"><img src="IMAGES/power-off.png" alt="" id="icon5" title="Logout"></a>
   </div>
   <div id="content">
   <form action="last.php" method="post" id="searchForm">
    <div id="wahuni">
        <div id="admin1">
          <h3>EMPOWERING FUTURES</h3>
        </div>
        <div id="admin2">
        <h3>UNLOCKING POTENTIAL</h3>
        </div>
        <div id="admin3">
        <h3>BUILDING BRIGHTER TOMORROW</h3>
        </div>
    </div>
    <div id="admin">
        <label for="search">Search:</label>
        <input type="search" id="search" name="result" placeholder="Search...">
        <button type="submit" name="submit1" id="submit1">Search</button>
        <script>
    // Get the input element by its ID
    const searchInput = document.getElementById("search");

    // Add an input event listener to the search input
    searchInput.addEventListener("input", function () {
        // Convert the input value to uppercase
        this.value = this.value.toUpperCase();
    });

    // Add a keypress event listener to the search input
    searchInput.addEventListener("keypress", function (e) {
        // Check if the key pressed is a number (0-9)
        if (/[0-9]/.test(e.key)) {
            // Prevent entering numbers
            e.preventDefault();
        }
    });
</script>

    </div>
    </form>
   </div>
   </div>
   <div id="footer5">
    <p>Copyright ©2023 Students HghSchool Allocation System. All Rights Reserved.</p>
   </div>
</body>
</html>