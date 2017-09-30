<?php session_start(); ?>
<html>
<head>
  <link rel="icon" href="design/favicon.png" type="image/png" />
  <link type="text/css" rel="stylesheet" href="stylesheet.css">
  <title>MA Settings</title>
</head>
<body>
  <?php
  if (!isset($_SESSION["userid"])) {
    echo "<script>
    window.alert('Please Log in');
    window.location.href='.';
    </script>";
  }
   ?>
  <ul>
    <li><a class="active" href="settings.php">Settings</a></li>
    <li><a href="data.php">Data</a></li>
    <li><a href="subscriptions.php">Subscriptions</a></li>
    <li><a href="index.php">Login</a></li>
    <li style="float:left"><a style="padding: 0px 16px" href="."><img src="design/title.png" alt="title" width=100 height=100/></a></li>
  </ul>
  <?php

  //get the name of the user to echo in a header
  $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");
  $stocks = $conn->query("select * from instruments inner join subscriptions on (subscriptions.instrumentid=instruments.instrumentid) where subscriptions.userid=".$_SESSION['userid']);
  $users = $conn->query("select * from usertbl where userid=".$_SESSION['userid'])
  ?>
  <div class=textbox>
    <h2><?php

    //echo the name of the user to personalize the page
    $user = $users->fetch_assoc();
    echo $user["firstname"]." ".$user["lastname"];
    ?></h2>
    <h3 style='text-align:left'>Change Password:</h3>
    <form action='password.php' method=post>
      <input type="password" placeholder="Old Password" name="oldpass" /><br />
      <input type="password" placeholder="New Password" name="newpass" /><br />
      <input type='submit' />
    </form>
    <hr />
    <h3 style='text-align:left'>Unsubscribe:</h3>
    <?php

    //if there are no subscriptions, output a message
    if ($stocks->num_rows==0) {
      echo "<p>
      No Subscriptions
      </p>";
    }
    //else echo a list of them with checkboxes
    else {
      echo "<div class=textbox style='text-align:center'><form action=unsubscribe.php method=post ><hr />";
      while ($stock = $stocks->fetch_assoc()) {
        echo $stock["description"].": <input style='float:right' type=checkbox name=".$stock["instrumentid"]." /><hr />";
      }
      echo "<input type=submit value=Unsubscribe /></form></div>";
    }
    ?>
    <hr />
    <h3 style='text-align:left'>Delete Account:</h3>
    <form action='delete.php' >
      <input type='submit' />
    </form>
  </div>
</body>
</html>
