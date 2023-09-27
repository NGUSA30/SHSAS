<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="selform.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selform</title>
</head>
<body>
  <style>
    #summary{
      display:flex;
    }
    #user{
      background-color:  rgb(35, 35, 39);
      color: white;
      text-align: justify;
      width: max-content;
      font-family: serif;
      padding:5px;
      border-radius: 0.3em;
    }
    #sum{
      margin-right: 350px;
      margin-left: 50px;
      margin-top: 10px;
    }
    #sum2{
      margin-top: 10px;
      margin-right: 320px;
    }
    #sum3{
      margin-top: 10px;
    }
    #image{
      width: 90%;
      height:500px;
      filter: grayscale(30%) sepia(50%) opacity(0.5);
    
    }
    
    h1{
      position:absolute;
      margin-left:400px;
      margin-top: 200px;
      line-height: 70px;
      font-size:72px;
      letter-spacing: 2px;
      color:darkblue;
    }
    #head4{
      position:absolute;
      top:450px;
      margin-left:430px;
      font-weight: bolder;
      font-size: 20px;
      color:darkblue;

    }
    h2{
      position:absolute;
      margin-left:450px;
      margin-top: 250px;
      line-height:50px ;
      font-size: 36px;
      letter-spacing: 1px;
      color:darkblue;
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
    <a href="allocation.php" ><img src="IMAGES/allocation.png" alt="" id="icon3" title="myAllocation"><br>
     <a href="change.php"><img src="IMAGES/password.png" alt="" id="icon4" title="change password"></a><br>
     <a href="login.php"><img src="IMAGES/power-off.png" alt="" id="icon5" title="Logout"></a>
   </div>
   <div id="content">
         <h1>Welcome to</h1><h2><small>School Allocation System</small></h2>
          <p id="head4">Ensure a smooth transition to your new school</p>
        <div>
          <img src="IMAGES/studd.png" alt="" id="image">
        </div>
   </div>
   </div>
   <div id="footer">
    <div id="footer1">
      <span>Support Desk</span><br>
      <img src="IMAGES/customer-service.png" id="service" alt="service"><br>
      <span id="number">0756220535</span><br>
      <span id="number">0645362874</span>
    </div>
    <div id="footer2">
      <span>Email</span><br>
      <img src="IMAGES/message.png" id="service2" alt="email"><br>
      <P>SHSAS@gmail.com</P>
    </div>
    <div id="footer3">
      <span>Mail Box</span>
            <img src="IMAGES/mailbox.png" id="service3" alt="mailbox"><br>
            <span>P.O.BOX 210</span><br>
            <span>Dar Es Salaam</span>
    </div>
    <div id="footer4">
         <p id="motto1">Welcome to High School</p>
         <img src="IMAGES/teacher.png" id="teacher" alt="teacher">
         <p id="motto2">Perform, learn, and excel for a better tomorrow.</p>
    </div>
   </div>
   <div id="footer5">
    <p>Copyright ©2023 Students HghSchool Allocation System. All Rights Reserved.</p>
   </div>
</body>
</html>