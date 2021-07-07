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
            http_response_code(201);
            echo ('Insert successful');
        } else {
            http_response_code(400);
            echo ('Invalid Values');
        }
    } else {
        http_response_code(401);
        echo ("Api key wrong");
    }
} else {
    http_response_code(500);
    echo ("Request failed");
}
