<?php
  session_start();
  $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");
  if ($_POST["login"] == "Login") {
    $users=$conn->query("select * from usertbl where firstname='".$_POST["firstname"]."' and lastname='".$_POST["lastname"]."'");
    if ($users->num_rows==0) {
      header("Location: .");
    }
    else {
      while ($user = $users->fetch_assoc()) {
        if (password_verify($_POST["password"], $user["password"])) {
          $_SESSION['userid'] = $user['userid'];
          header("Location: data.php");
        }
        else {
          header("Location: .");
        }
      }
    }
  }
  elseif ($_POST["login"] == "Sign Up") {
      if (strchr($_POST["firstname"],';') !== FALSE or strchr($_POST["firstname"],',') !== FALSE or strchr($_POST["lastname"],';') !== FALSE or strchr($_POST["lastname"],',') !== FALSE) {
        header("Location: .");
      }
      $conn->query("insert into usertbl (firstname, lastname, password)
      values ('".$_POST["firstname"]."','".$_POST["lastname"]."','".password_hash($_POST["password"], PASSWORD_DEFAULT)."')");
      $users=$conn->query("select userid from usertbl where firstname='".$_POST["firstname"]."' and lastname='".$_POST["lastname"]."'");

      while($user=$users->fetch_assoc()) {
        $_SESSION['userid'] = $user['userid'];
        header("Location: subscriptions.php?h=".strchr($_POST["firstname"],';'));
      }
  }
?>
