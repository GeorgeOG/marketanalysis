<?php
  session_start();

  //basically the same as subscribe.py, just with delete statements instead of insert
  $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");
  foreach ($_POST as $stock => $selected) {
    if ($selected=='on') {
      $conn->query('delete from subscriptions where instrumentid="'.$stock.'" and userid='.$_SESSION["userid"]);
    }
  }

  //return to settings page
  header("Location: settings.php");
 ?>
