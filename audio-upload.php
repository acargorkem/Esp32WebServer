<?php
require_once 'includes/database.php';

if (isset($_POST['audio_name'])) {

    $data = $_POST["audio_data"];

    $audio_name = $_POST["audio_name"];

    $file_path = 'audio/' . $audio_name . '.txt';

    if (file_exists($file_path)) {
        http_response_code(409);
        echo ('File already Exist');
    } else {
        if ($data != null) {

            $myfile = fopen($file_path, 'w') or die('Unable to open file');
            fwrite($myfile, $data);

            $query = "INSERT INTO audio_table (audio_name, audio_path) VALUES ('$audio_name', '$file_path')";
            $insertResult = mysqli_query($conn, $query);
            http_response_code(201);
            echo ('Audio data uploaded successfully');
            fclose($myfile);
        } else {
            echo 'An error occurred.';
        }
    }
} else {
    http_response_code(400);
    echo ('Request body is missing');
}
