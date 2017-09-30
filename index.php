<?php session_start(); ?>
<html>
<head>
  <link rel="icon" href="design/favicon.png" type="image/png" />
  <link type="text/css" rel="stylesheet" href="stylesheet.css">
  <title>MA Login</title>
</head>
<body>
  <!--this is the nav bar at the top-->
  <ul>
    <li><a href="settings.php">Settings</a></li>
    <li><a href="data.php">Data</a></li>
    <li><a href="subscriptions.php">Subscriptions</a></li>
    <li><a class="active" href="index.php">Login</a></li>
    <li style="float:left"><a style="padding: 0px 16px" href="."><img src="design/title.png" alt="title" width=100 height=100/></a></li>
  </ul>
  <div class="textbox">
  <h1>Login or Sign up!</h1>
  <hr />
  <!--the form to login-->
  <h2>Login</h2>
  <form action="login.php" method="post">
    First Name:<br />
    <input type="text" name="firstname" /><br />
    Last Name:<br />
    <input type="text" name="lastname" /><br />
    Password:<br />
    <input type="password" name="password" /><br />
    <input type="submit" value="Login" name=login /><br />
  </form>
  <!--the form to sign up-->
  <h2>Sign Up</h2>
  <form action="login.php" method="post">
    First Name:<br />
    <input type="text" name="firstname" /><br />
    Last Name:<br />
    <input type="text" name="lastname" /><br />
    Password:<br />
    <input type="password" name="password" /><br />
    <input type="submit" value="Sign Up" name=login /><br />
  </form>
  </div>
</body>
</html>
