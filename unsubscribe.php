<?php
  session_start();
  $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");
  foreach ($_POST as $stock => $selected) {
    if ($selected=='on') {
      $conn->query('delete from subscriptions where instrumentid="'.$stock.'" and userid='.$_SESSION["userid"]);
    }
  }
  header("Location: settings.php");
 ?>
