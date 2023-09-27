<!DOCTYPE html>
<html lang="en">
<head>
    <link href="selform.css" rel="stylesheet">
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
   <div id="ovar">
    <div id="side">
     <a href="selform.php" ><img src="IMAGES/dashboard.png" alt="" id="icon1" title="dashboard"></a><br>
     <a href="form.php"><img src="IMAGES/google-forms.png" alt="" id="icon2" title="selform"></a><br>
     <a href="allocation.php" ><img src="IMAGES/allocation.png" alt="" id="icon3" title="myAllocation"><br>
      <a href="change.php"><img src="IMAGES/password.png" alt="" id="icon4" title="change password"></a><br>
      <a href="login.php"><img src="IMAGES/power-off.png" alt="" id="icon5" title="Logout"></a>
    </div>
    <div>
        
        <fieldset id="set">
            <legend>SESSION 1</legend>
            <form method="post" action="action1.php" enctype="multipart/form-data" onsubmit="validateForm()">
            <input type="text" placeholder="Full Name" id="candidate" name="candidate"><br>
            <input type="text" placeholder="candidate Number" id="number" name="number"><br>
            <input type="text" placeholder="Email" id="mail" name="useremail"><br>
            <p>select region you want to go</p>
            <select name='mkoa'>
                <option value="region" disabled>Select region </option>
                <option value="PWANI">PWANI</option>
                <option value="TABORA">TABORA</option>
                <option value="MOROGORO">MOROGORO</option>
                
            </select><br><br>
            <button type="submit" id="next1" name="next1">NEXT</button>
            <?php if (isset($loginError)) { ?>
        <div class="notification"> Invalid username or password </div>
    <?php } ?>
            </form>
        </fieldset>
        </form>
        
    </div>
</body>
</html>