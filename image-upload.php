<?php
require_once 'includes/database.php';

if (isset($_POST['img_name'])) {

    $data = $_POST["img_file"];   

    // $data = base64_decode($data);

    $image =  imagecreatefromstring($data);

    $image_name = $_POST["img_name"];

    $file_path = "images/" . $image_name .'.jpg';

    if (file_exists($file_path)) {
        http_response_code(409);
        echo ('Image already Exist');
    } else {
        if ($image !== false) {
            // saves an image to specific location
            header('Content-type: image/jpg');            
            $resp = imagejpeg($image, $_SERVER['DOCUMENT_ROOT'] . '/IOT-Platform/' . $file_path);

            $query = "INSERT INTO image_table (image_name, image_path) VALUES ('$image_name', '$file_path')";
            $insertResult = mysqli_query($conn, $query);
            http_response_code(201);
            echo ('Image uploaded successfully');


            // frees image from memory
            imagedestroy($image);
        } else {
            // show if any error in bytes data for image
            echo 'An error occurred.';
        }
    }
} else {
    http_response_code(400);
    echo ('Request body is missing');
}
