<?php
require_once 'includes/database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESP32 Web Server</title>
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>

<body>
<!-- <script>
var prevScrollpos = window.pageYOffset;
window.onscroll = function() {
var currentScrollPos = window.pageYOffset;
  if (prevScrollpos > currentScrollPos) {
    document.getElementById("header").style.display = "block";
  } else {
    document.getElementById("header").style.display = "none";
  }
  prevScrollpos = currentScrollPos;
}
</script> -->
    <header id="header">
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="sensor.php">Sensor</a></li>
                <li><a href="camera.php">Camera</a></li>
                <li><a href="ota.php">OTA Programming</a></li>
            </ul>
        </nav>        
    </header>
    <div class="content">
        