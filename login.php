<?php
  session_start();
  $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");

  //if the user logged in
  if ($_POST["login"] == "Login") {

    //get list of users with the same firstname and lastname
    $users=$conn->query("select * from usertbl where firstname='".$_POST["firstname"]."' and lastname='".$_POST["lastname"]."'");

    //if no such users exist, return home
    if ($users->num_rows==0) {
      header("Location: .");
    }
    else {

      //otherwise, verify the password
      while ($user = $users->fetch_assoc()) {
        if (password_verify($_POST["password"], $user["password"])) {

          //if successful, save the userid and go to the data page
          $_SESSION['userid'] = $user['userid'];
          header("Location: data.php");
        }
        else {

          //if the password is wrong, go home
          header("Location: .");
        }
      }
    }
  }

  //if the user signed ip
  elseif ($_POST["login"] == "Sign Up") {

      //check whether the names contain illegal characters
      if (strchr($_POST["firstname"],';') !== FALSE or strchr($_POST["firstname"],',') !== FALSE or strchr($_POST["lastname"],';') !== FALSE or strchr($_POST["lastname"],',') !== FALSE) {
        header("Location: .");
      }

      //enter new user into the database
      $conn->query("insert into usertbl (firstname, lastname, password)
      values ('".$_POST["firstname"]."','".$_POST["lastname"]."','".password_hash($_POST["password"], PASSWORD_DEFAULT)."')");
      $users=$conn->query("select userid from usertbl where firstname='".$_POST["firstname"]."' and lastname='".$_POST["lastname"]."'");

      #save the userid for the session, and go to the subscriptions page
      while($user=$users->fetch_assoc()) {
        $_SESSION['userid'] = $user['userid'];
        header("Location: subscriptions.php");
      }
  }
?>
