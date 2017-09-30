<?php
  session_start();
  $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");

  //select the user's row from the database
  $users = $conn->query("select * from usertbl where userid=".$_SESSION['userid']);
  $user = $users->fetch_assoc();

  //if the password is correct
  if (password_verify($_POST['oldpass'], $user['password'])) {

    //update the password, and issue an alert
    $conn->query('update usertbl set password="'.password_hash($_POST['newpass'], PASSWORD_DEFAULT).'" where userid='.$_SESSION['userid']);
    echo "<script>
    window.alert('Password Updated');
    window.location.href = 'settings.php';
    </script>";
  }
  else {

    //if the password's wrong issue an alert and return to the settings page
    echo "<script>
    window.alert('Password Incorrect');
    window.location.href = 'settings.php';
    </script>";
  }
 ?>
