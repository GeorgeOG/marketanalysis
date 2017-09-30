<?php
  session_start();

  //this just deletes the requested user from the database
  $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");
  $conn->query("delete from usertbl where userid=".$_SESSION['userid']);

  //resets the userid variable
  $_SESSION['userid'] =NULL;

  //takes the user home
  header('Location: .');
 ?>
