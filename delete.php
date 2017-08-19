<?php
  session_start();
  $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");
  $conn->query("delete from usertbl where userid=".$_SESSION['userid']);
  $_SESSION['userid'] =NULL;
  header('Location: .');
 ?>
