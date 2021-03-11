<?php
$data = $_REQUEST;
//echo json_encode($data);

function map($value, $fromLow, $fromHigh, $toLow, $toHigh)
{
    $fromRange = $fromHigh - $fromLow;
    $toRange = $toHigh - $toLow;
    $scaleFactor = $toRange / $fromRange;

    // Re-zero the value within the from range
    $tmpValue = $value - $fromLow;
    // Rescale the value to the to range
    $tmpValue *= $scaleFactor;
    // Re-zero back to the to range
    return $tmpValue + $toLow;
}

function speed($angle, $strength){
    //left
    if (175 <= $angle && $angle <= 185) {
        $leftSpeed = $strength;
        $rightSpeed = $strength;
    }
    //forward
    if (85 <= $angle && $angle <= 95) {
        $leftSpeed = $strength;
        $rightSpeed = $strength;
    }
    //right
    if ((0 <= $angle && $angle <= 5) || (355 <= $angle && $angle < 360)) {
        $leftSpeed = $strength;
        $rightSpeed = $strength;
    }
    //back
    if (265 <= $angle && $angle <= 275) {
        $leftSpeed = $strength;
        $rightSpeed = $strength;
    }
    //forward diagonal
    if ((5 < $angle && $angle < 85) || (95 < $angle && $angle < 175)) {
        $leftSpeed = ((180 - $angle) * $strength) / 180;
        $rightSpeed = ($angle / 180) * $strength;
    }
    //backward diagonal
    if ((185 < $angle && $angle < 265) || (275 < $angle && $angle < 355)) {
        $leftSpeed = (($angle - 180) * $strength) / 180;
        $rightSpeed = ((360 - $angle) * $strength) / 180;
    }
    return array($leftSpeed, $rightSpeed);
}
if(isset($data['x'])){
$x = $data['x'];
$y = $data['y'];
$angle = $data['angle'];
$strength = $data['strength'];
$leftSpeed = speed($angle, $strength)[0];
$rightSpeed = speed($angle, $strength)[1];
$mappedLeft = map($leftSpeed, 0, 100, 0, 255);
echo (" Left = $mappedLeft ");

$mappedRight = map($rightSpeed, 0, 100, 0, 255);
echo (" Right = $mappedRight ");
}


$data2 = file_get_contents('php://input');

// echo($data2);
echo json_encode($data2);