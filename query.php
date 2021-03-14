<?php
require_once 'includes/database.php';


if (isset($_POST['temperature'], $_POST["humidity"], $_POST["altitude"], $_POST["pressure"], $_POST['api_key'])) {
    $api_key_value = $_POST['api_key'];
    if ($api_key_value == 'gorkem') {
        $temperature = $_POST["temperature"];
        $humidity = $_POST["humidity"];
        $altitude = $_POST["altitude"];
        $pressure = $_POST["pressure"];
        if (($temperature > -60) && ($temperature < 60)) {
            $query = "INSERT INTO bme280_values (temperature, humidity, altitude, pressure) VALUES ('$temperature', '$humidity' ,'$altitude', '$pressure' )";
            $insertResult = mysqli_query($conn, $query);
            echo ('Insert successful');
        } else {
            echo ('Not valid Values');
        }
    } else {
        echo ("Api key wrong");
    }
} else {
    echo ("Request failed");
}
