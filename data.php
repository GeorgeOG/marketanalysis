<?php session_start(); ?>
<html>
<head>
  <link rel="icon" href="design/favicon.png" type="image/png" />
  <link type="text/css" rel="stylesheet" href="stylesheet.css">
  <title>MA Data</title>
</head>
<body>
  <?php
  if (!isset($_SESSION["userid"])) {
    echo "<script>
    window.alert('Please Log in');
    window.location.href='.';
    </script>";
  }
   ?>
  <ul>
    <li><a href="settings.php" >Settings</a></li>
    <li><a class=active href="data.php" >Data</a></li>
    <li><a href="subscriptions.php" >Subscriptions</a></li>
    <li><a href="index.php" >Login</a></li>
    <li style="float:left"><a style="padding: 0px 16px" href="index.php"><img src="design/title.png" alt="title" width=100 height=100/></a></li>
  </ul>
  <br />
  <hr />
  <br />
  <?php
    $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");
    $stocks = $conn->query("select * from instruments inner join subscriptions on (subscriptions.instrumentid=instruments.instrumentid) where subscriptions.userid='".$_SESSION['userid']."'");
    if ($stocks->num_rows==0) {
      echo "<p>
      No Subscriptions found
      </p>";
    }
    else {
      echo "<ul class=menu>";
      while ($stock = $stocks->fetch_assoc()) {
        echo "<li class=menu_item>
        <a class=menu_item_text href=data.php?stock=".$stock["instrumentid"].">".$stock["description"]."</a>
        </li>";
      }
      echo "</ul>";
    }
    date_default_timezone_set('UTC');
    if (isset($_GET['stock'])) {
      $p = exec('/usr/local/bin/python3 /Library/WebServer/Documents/marketanalysis/analyse.py '.$_GET['stock']);
      if (($handle = fopen(explode('/', $_GET['stock'])[1].'.csv', 'r')) !== FALSE) {
        $data = array();
        while (($row = fgetcsv($handle, 30, ",")) !== FALSE) {
          $data[] = array(new DateTime($row[0]), $row[1]);
        }
      }
      unlink(explode('/', $_GET['stock'])[1].'.csv');

      $stocks = $conn->query("select * from instruments");

      while ($stock = $stocks->fetch_assoc()) {
        if ($stock['instrumentid'] == $_GET['stock']) {
          echo '<h2 style="margin-left:25%">'.$stock['description'].'</h2>';
          $currency = $stock['currency'];
        }
      }
    }

   ?>
   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
   <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart','controls']});
    google.charts.setOnLoadCallback(drawDashboard);
    function drawDashboard() {
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Date');
        data.addColumn('number', '<?php echo $_GET['stock']; ?>');
        data.addRows([ <?php
          foreach ($data as $day) {
            echo '[ new Date('.$day[0]->format('Y, m ,d').'), '.$day[1].'],';
          }
        ?>]);

        var dashboard = new google.visualization.Dashboard(document.getElementById('dashboard_div'));

        var lineChart = new google.visualization.ChartWrapper({
          'chartType': 'LineChart',
          'containerId': 'chart_div',
          'options': {
            'title':'Price',
            'curvetype':'function',
            'vAxis': {
              'title': 'Price in <?php echo $currency; ?>'
            },
            'hAxis': {
              'title': 'Date'
            }
          }
        });

        var timeSlider = new google.visualization.ControlWrapper({
          'controlType': 'DateRangeFilter',
          'containerId': 'filter_div',
          'options': {
            'filterColumnLabel': 'Date'
          }
        });

        dashboard.bind(timeSlider, lineChart);

        dashboard.draw(data);
      }
   </script>
   <div id='dashboard_div' style='margin:auto; width:70%;'>
   <div id='filter_div'></div>
   <div id='chart_div' style='height: 50%'></div>
   </div>
   <p style='text-align:center'>
     Prediction: <?php echo $p; ?>
   </p>
</body>
</html>
