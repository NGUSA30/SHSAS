<!DOCTYPE html>
<html lang="en">
<head>
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
   <div id="pass1">
    <div id="ovar">
        <div id="side">
         <a href="selform.php" ><img src="IMAGES/dashboard.png" alt="" id="icon1" title="dashboard"></a><br>
         <a href="form.php"><img src="IMAGES/google-forms.png" alt="" id="icon2" title="selform"></a><br>
         <a href="allocation.php" ><img src="IMAGES/allocation.png" alt="" id="icon3" title="myAllocation"><br>
          <a href="change.php"><img src="IMAGES/password.png" alt="" id="icon4" title="change password"></a><br>
          <a href="login.php"><img src="IMAGES/power-off.png" alt="" id="icon5" title="Logout"></a>
        </div>
    <div id="pass2">
        <fieldset>
            <legend>CHANGE PASSWORD</legend>
            <form method="post" action="action5.php">
        <input type="text" id="password1" placeholder="old password" name="oldpassword"><br>
        <input type="text" id="password1" placeholder="email" name="email"><br>
        <input type="text" id="password2" placeholder="new Password" name="newpassword"><br><br>
        <button type="submit" id="button" name="confirm">CONFIRM</button>
            </form>
        </fieldset>
    </div>
   </div>
   </div>
</body>
</html>