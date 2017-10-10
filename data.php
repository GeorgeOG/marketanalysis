<?php session_start(); ?>
<html>
<head>
  <link rel="icon" href="design/favicon.png" type="image/png" />
  <link type="text/css" rel="stylesheet" href="stylesheet.css">
  <title>MA Data</title>
</head>
<body>
    <?php
  //if no user has signed in, a javascript error message appears and the user will be taken home
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
    //connection set up, and list of stocks queried from the database
    $conn = new mysqli("localhost", "georgegarber", "password", "marketanalysisdb");
    $stocks = $conn->query("select * from instruments inner join subscriptions on (subscriptions.instrumentid=instruments.instrumentid) where subscriptions.userid='".$_SESSION['userid']."'");

    //message displayed if no stocks found
    if ($stocks->num_rows==0) {
        echo "<p>
      No Subscriptions found
      </p>";
    } //otherwise list of stocks generated
    else {
        echo "<ul class=menu>";

        //iterate through all subscribed stocks
        while ($stock = $stocks->fetch_assoc()) {
          //has the prediction been found yet?
            if (!isset($_SESSION['predictions'][$stock["instrumentid"]])) {
                //get prediction, delete file created
                $p = explode(',', exec('/usr/local/bin/python3 /Library/WebServer/Documents/marketanalysis/analyse.py '.$stock["instrumentid"]))[0];
                $_SESSION['predictions'][$stock["instrumentid"]] = $p;
                unlink(explode('/', $stock["instrumentid"])[1].'.csv');
            }

          //set font colour variable
            $fcolour = '#e2e2e2';

          //switch to determine correct colour for prediction
            switch ($_SESSION['predictions'][$stock["instrumentid"]]) {
                case '-1.0 ':
                    $colour = 'red';
                    break;
                case '0.0 ':
                    $colour = 'yellow';
                    $fcolour = '#505050';
                    break;
                case '1.0 ':
                    $colour = 'green';
                    break;
            }

          //create the list entry
            echo "<li style='background-color: ".$colour."' class=menu_item>
          <a style='color: ".$fcolour."' class=menu_item_text href=data.php?stock=".$stock["instrumentid"].">".$stock["description"]."</a>
          </li>";
        }
        echo "</ul>";
    }

    //set the time
    date_default_timezone_set('UTC');
    $today = new DateTime();
    $year = $today->modify('-3 days')->format('Y');

    //if a user has clicked on a stock
    if (isset($_GET['stock'])) {
      //run analyse.py (storing the prediction in $p) and collect all the data in a new array
        $p = exec('/usr/local/bin/python3 /Library/WebServer/Documents/marketanalysis/analyse.py '.$_GET['stock']);
        if (($handle = fopen(explode('/', $_GET['stock'])[1].'.csv', 'r')) !== false) {
            $data = array();
            while (($row = fgetcsv($handle, 50, ",")) !== false) {
                $data[] = array(new DateTime($row[0]), $row[1], $row[2]);
            }
        }
        unlink(explode('/', $_GET['stock'])[1].'.csv');

        //determine the currency of the seleted stock, and print out it's description
        $stocks = $conn->query("select * from instruments");
        while ($stock = $stocks->fetch_assoc()) {
            if ($stock['instrumentid'] == $_GET['stock']) {
                echo '<h2 style="margin-left:25%">'.$stock['description'].'</h2>';
                $currency = $stock['currency'];
            }
        }
        $upToDate = false;
    }
    ?>
   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
   <script type="text/javascript">

    //initialize google charts packages
    google.charts.load('current', {'packages':['corechart','controls']});
    google.charts.setOnLoadCallback(drawDashboard);

    //function to create the slider and the graph
    function drawDashboard() {

        //this creates the data table with the data from the array we created earlier
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Date');
        data.addColumn('number', '<?php echo $_GET['stock']; ?>');
        data.addColumn('number', 'SAR')
        data.addRows([ <?php
        foreach ($data as $day) {
            echo '[ new Date('.$day[0]->format('Y, m ,d').'), '.$day[1].', '.$day[2].'],';
            if ($day[0]->format('Y') == $year) {
                   $upToDate = true;
            }
        }
        ?>]);

        //assocciate the dashboard with an html element
        var dashboard = new google.visualization.Dashboard(document.getElementById('dashboard_div'));

        //initialze the graph and the slider
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
            },
            'colors': ['blue', '#f47cff']
          }
        });
        var timeSlider = new google.visualization.ControlWrapper({
          'controlType': 'DateRangeFilter',
          'containerId': 'filter_div',
          'options': {
            'filterColumnLabel': 'Date'
          }
        });

        //connect the two elements and draw them
        dashboard.bind(timeSlider, lineChart);
        dashboard.draw(data);

        today = new Date()
        twoYears = function() {
          timeSlider.setState({'lowValue': new Date().setDate(today.getDate()-731), 'highvalue': today});
          timeSlider.draw();
        };
        oneYear = function() {
          timeSlider.setState({'lowValue': new Date().setDate(today.getDate()-366), 'highvalue': today});
          timeSlider.draw();
        };
        sixMonths = function() {
          timeSlider.setState({'lowValue': new Date().setDate(today.getDate()-184), 'highvalue': today});
          timeSlider.draw();
        };
        twoMonths = function() {
          timeSlider.setState({'lowValue': new Date().setDate(today.getDate()-62), 'highvalue': today});
          timeSlider.draw();
        };
        oneMonth = function() {
          timeSlider.setState({'lowValue': new Date().setDate(today.getDate()-32), 'highvalue': today});
          timeSlider.draw();
        };
        oneWeek = function() {
          timeSlider.setState({'lowValue': new Date().setDate(today.getDate()-8), 'highvalue': today});
          timeSlider.draw();
        };
    }
   </script>
   <div id='dashboard_div' style='margin:auto; width:60%; background-color: white; <?php if (!isset($_GET["stock"])) {
        echo "display: none;";
} ?>' >
   <div id='filter_div' style="display: none"></div>
   <table style='margin:auto; border-spacing: 10px'>
   <tr>
   <td><button onclick="twoYears();">Two Years</button></td>
   <td><button onclick="oneYear();">One Year</button></td>
   <td><button onclick="sixMonths();">Six Months</button></td>
   <td><button onclick="twoMonths();">Two Months</button></td>
   <td><button onclick="oneMonth();">One Month</button></td>
   <td><button onclick="oneWeek();">One Week</button></td>
   </tr>
   </table>
   <div id='chart_div' style='height: 50%'></div>
   <p style='text-align:center'>
        <?php if (isset($_GET["stock"])) {
                $ps = explode(',', $p);
                echo 'Prediction: '.$ps[0].', Accuracy: '.$ps[1].'%';
                if (!$upToDate) {echo "<br />Incomplete data, may display incorrectly.";}
            } ?></p>
   </div>
</body>
</html>
