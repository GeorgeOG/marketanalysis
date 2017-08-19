<?php
  session_start();
  $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");
  $users = $conn->query("select * from usertbl where userid=".$_SESSION['userid']);
  $user = $users->fetch_assoc();
if (password_verify($_POST['oldpass'], $user['password'])) {
    $conn->query('update usertbl set password="'.password_hash($_POST['newpass'], PASSWORD_DEFAULT).'" where userid='.$_SESSION['userid']);
    echo "<script>
    window.alert('Password Updated');
    window.location.href = 'settings.php';
    </script>";
  }
  else {
    echo "<script>
    window.alert('Password Incorrect');
    window.location.href = 'settings.php';
    </script>";
  }
 ?>
