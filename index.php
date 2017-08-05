<html>
<head>
  <link rel="icon" href="design/favicon.png" type="image/png" />
  <title>Market Analysis Coursework</title>
</head>
<body>
  <img src="design/title.png" alt="title">
  <p>
  <?php
    $name='';
    echo exec('/usr/local/bin/python /Library/WebServer/Documents/marketanalysis/data.py '.$name);
   ?>
 </p>
</body>
</html>
