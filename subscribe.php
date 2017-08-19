<?php
  session_start();
  $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");
  foreach ($_POST as $stock => $selected) {
    if ($selected=='on') {
      $conn->query('insert into subscriptions (instrumentid, userid) values ("'.$stock.'",'.$_SESSION["userid"].')');
    }
  }
  header("Location: subscriptions.php");
 ?>
