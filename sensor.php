<?php
require_once 'includes/header.php';
?>
<h1>EPS32 Sensor Values</h1>
<?php

$sql = "(SELECT * FROM bme280_values ORDER BY timestamp DESC LIMIT 20) ORDER BY Id ASC";
$result = mysqli_query($conn, $sql);
$rowCount = mysqli_num_rows($result);
$allColumns = array();
if ($rowCount > 0) {
    echo '<table class="data-table">
        <tr class="data-heading">';  //initialize table tag
    while ($column = mysqli_fetch_field($result)) {
        echo '<th>' . $column->name . '</td>';  //get field name for header
        array_push($allColumns, $column->name);  //save those to array
    }
    echo '</tr>'; //end tr tag
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($allColumns as $item) {
            echo '<td>' . $row[$item] . '</td>'; //get items using property value
        }
        // echo $row['temperature'];
    }
    echo "</table>";
    echo " * Last 20 data are shown.";
} else {
    echo ("No result");
}


?>


<?php
require_once 'includes/footer.php';
?>