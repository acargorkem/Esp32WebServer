<?php

// database parameters for localhost
$dbHost = "localhost";
$dbUser = "root";
$dbPass = "gorkem07";
$dbName = "esp32db";
$port = 3307;

//database connection
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $port);


if (!$conn) {
    die("Connection Failed");

} 