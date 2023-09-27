<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="reg.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register_form</title>
    <style>
        #name{
            width:300px;
            height:25px;
             margin-bottom: 10px;
        }
        #phone{
            width:300px;
            height:25px;
             margin-bottom: 10px;
        }
        #password{
    width:300px;
    height:25px;
    margin-bottom: 10px;
}
#cno{
    width:300px;
    height:25px;
    margin-bottom: 10px;
}
#email{
    width:300px;
    height:25px;
    margin-bottom: 10px;
}#border{
    border:1px solid black;
    width:max-content;
    margin-left:auto;
    margin-right:auto;
    padding:10px;
    margin-top: 40px;
}

    </style>
</head>
<body>
    <div id="border">
     <div id="internal">
         <img src="IMAGES/school.png" id="pic" alt="high school">
         <p id="head">STUDENT HIGH SCHOOL ALLOCATION SYSTEM</p>
         <form method="post" action="ngusa.php">
         <input type="text" id="name" placeholder="Enter Fullname" name="fullName"><br>
         <input type="email" id="email" placeholder="Enter email" name="email"><br>
         <input type="password" id="password" placeholder="Enter password" name="passwords"><br>
         <input type="text" id="cno" placeholder="Enter candidate number" name="cno"><br>
         <input type="tel" id="phone" placeholder="Enter phone number" name="phone"><br>
         <p>Choose gender</p>
         <select name="gender">
         <option value="select gender" disabled>select gender</option>
            <option value="male">male</option>
            <option value="female">female</option>
         </select><br><br>
         <input type="submit" id="register" value="Register" >
         <p>Click here <a href="login.php" id="link1">login</a> if you have an account</p>
         </form>
     </div>
    </div>
</body>
</html>