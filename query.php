<?php
require_once 'includes/database.php';




if (isset($_GET['temperature'], $_GET["humidity"], $_GET["altitude"], $_GET["pressure"], $_GET['apikey'])) {
        $api_key_value = $_GET['apikey'];
    if ($api_key_value == 'gorkem') {
        $temperature = $_GET["temperature"];
        $humidity = $_GET["humidity"];
        $altitude = $_GET["altitude"];
        $pressure = $_GET["pressure"];
        $query = "INSERT INTO bme280_values (temperature, humidity, altitude, pressure) VALUES ('$temperature', '$humidity' ,'$altitude', '$pressure' )";
        $insertResult = mysqli_query($conn, $query);
    } else {
        echo ("api key wrong");
    }
} else {
    echo ("request failed");
}
