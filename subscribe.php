<?php
  session_start();
  $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");

  //for all stocks subscribed to
  foreach ($_POST as $stock => $selected) {

    //if selected
    if ($selected=='on') {

      //create and execute insertion statement
      $conn->query('insert ignore into subscriptions (instrumentid, userid) values ("'.$stock.'",'.$_SESSION["userid"].')');
    }
  }

  //return to the subscriptions page
  header("Location: subscriptions.php");
 ?>
