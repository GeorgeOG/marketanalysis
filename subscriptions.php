<?php session_start(); ?>
<html>
<head>
  <link rel="icon" href="design/favicon.png" type="image/png" />
  <link type="text/css" rel="stylesheet" href="stylesheet.css">
  <title>MA Subscribe</title>
</head>
<body>
  <?php

  //make sure the user is logged in
  if (!isset($_SESSION["userid"])) {
    echo "<script>
    window.alert('Please Log in');
    window.location.href='.';
    </script>";
  }
   ?>
  <ul>
    <li><a href="settings.php" >Settings</a></li>
    <li><a href="data.php" >Data</a></li>
    <li><a class="active" href="subscriptions.php" >Subscriptions</a></li>
    <li><a href="index.php" >Login</a></li>
    <li style="float:left"><a style="padding: 0px 16px" href="index.php"><img src="design/title.png" alt="title" width=100 height=100/></a></li>
  </ul>
  <div class="textbox">
    <h1>Search for Stocks</h1>
    <form action="subscriptions.php" method="get" style="text-align:left">
      <input type=text placeholder=Search name=search />
      <input type=submit value="Search" />
    </form>
  </div>
  <?php

    //if the user has entered a search term
    if (isset($_GET["search"])) {
      //make sure there are no suspicious characters in the search
      if (strchr($_GET['search'],';') !== FALSE or strchr($_GET["search"],',') !== FALSE) {
        echo "Please only search words.";
      } else {
        //otherwise select relevant stocks from the database
        $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");
        $stocks = $conn->query("select * from instruments where description like '%".$_GET["search"]."%';");

        //ouput message if there are no stocks
        if ($stocks->num_rows==0) {
          echo "<p>
          No results
          </p>";
        }
        //else output a list of the relevant stocks
        else {
          echo "<div class=textbox style='text-align:center'><form action=subscribe.php method=post ><hr />";
          while ($stock = $stocks->fetch_assoc()) {
            echo $stock["description"].": <input style='float:right' type=checkbox name=".$stock["instrumentid"]." /><hr />";
          }
          echo "<input type=submit value=Subscribe /></form></div>";
        }
      }
    }
   ?>

</body>
</html>
