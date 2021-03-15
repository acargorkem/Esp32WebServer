<?php
require_once 'includes/header.php';
?>

<section class="section__title">
    <h2 class="section__title--sensor">
        Sensor Values
    </h2>
</section>
<section>
    <div class="section__filter">

        <form action="sensor.php" method="GET">
            <div class="section__filter section__filter--field">
                <label> Fields :</label>
                <input type="checkbox" id="checkbox_temperature" name="fields[]" value="temperature" checked>
                <label for="checkbox_temperature"> Temperature</label>
                <input type="checkbox" id="checkbox_pressure" name="fields[]" value="pressure" checked>
                <label for="checkbox_pressure"> Pressure</label> 
                <input type="checkbox" id="checkbox_altitude" name="fields[]" value="altitude" checked>
                <label for="checkbox_altitude"> Altitude</label> 
                <input type="checkbox" id="checkbox_humidity" name="fields[]" value="humidity" checked>
                <label for="checkbox_humidity"> Humidity</label>
            </div>

            <div class="section__filter section__filter--date">
                <label for="start">From : </label>
                <input class="section__filter--date" type="datetime-local" id="from" name="to_date" required>

                <label for="start">To : </label>
                <input class="section__filter--date" type="datetime-local" id="to" name="from_date" required>
            </div>

            <div class="section__filter section__filter--number">
                <label for="row_number">Select maximum number of rows : </label>
                <select id="row_number" name="row_number">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50" selected>50</option>
                    <option value="100">100</option>
                    <option value="250">250</option>
                    <option value="500">500</option>
                    <option value="1000">1000</option>
                    <option value="2000">2000</option>
                    <option value="5000">5000</option>
                </select>
                <button class="section__fiter--submit" type="submit" name="submit" id="submit"> Create data table and graph</button>
            </div>

        </form>
    </div>

</section>
<section class="section__data">

    <?php
    if (isset($_GET['submit'])) {
        if (isset(
            $_GET['fields'],
            $_GET['from_date'],
            $_GET['to_date'],
            $_GET['row_number']
        )) {

            $fields = $_GET['fields'];
            $fields_seperated = implode(',', $fields);
            $to_date = date("Y-m-d H:i:s", strtotime($_GET['to_date']));
            $from_date = date("Y-m-d H:i:s", strtotime($_GET['from_date']));
            $row_number = $_GET['row_number'];

            $sql = "
            SELECT $fields_seperated , timestamp 
            FROM bme280_values 
            WHERE Timestamp BETWEEN '$to_date' and '$from_date'
            ORDER BY timestamp DESC LIMIT $row_number";

            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            $allColumns = array();
            $json_data = array();

            if ($rowCount > 0) {
                echo ('<div class="section__data--table">');
                echo ('<table id="data-table" class = "section__data--table">
                <tr class = "section__data--heading">');  //initialize table tag
                while ($column = mysqli_fetch_field($result)) {
                    echo '<th class = "section__data--heading--value">' . $column->name . '</td>';  //get field name for header
                    array_push($allColumns, $column->name);  //save headers to array
                }
                echo '</tr>'; //end tr tag
                while ($row = mysqli_fetch_assoc($result)) {
                    $end = end($row);
                    echo '<tr class = "section__data--row">';
                    foreach ($allColumns as $item) {
                        echo '<td class = "section__data--value">' . $row[$item] . '</td>'; //get items using property value        
                        $json_array[$item] = $row[$item]; // saving temp array for row iteration
                    }
                    array_push($json_data, $json_array); //save data to array
                    echo '</tr>';
                }
                echo "</table>";
                echo ('<button id="button_export" class="data__table--button" id="export">Export Data table to Excel file</button>');
                echo ('</div>');
            } else {
                echo ("No result");
            }
        } else {
            echo ("Not enough parameter");
        }
    }
    ?>
    <div class="section__data--graph">
        <div class="data--graph__buttons">
            <button class="data--graph__button" value="temperature" id="button_temperature">Temperature</button>
            <button class="data--graph__button" value="pressure" id="button_pressure">Pressure</button>
            <button class="data--graph__button" value="altitude" id="button_altitude">Altitude</button>
            <button class="data--graph__button" value="humidity" id="button_humidity">Humidity</button>
        </div>
        <svg id="d3-graph" class="d3--graph" width="920" height="600"></svg>
    </div>
</section>
<script>
    var data;
    $(document).ready(function() {
        /*
         * setting <input tpye="datetime-local"> options
         * current time calculated and converted to ISO string format with first 16 characters
         */
        let dateTo = document.getElementById('to');
        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        dateTo.value = now.toISOString().slice(0, 16);

        let dateFrom = document.getElementById('from');
        dateFrom.value = '2021-02-01T08:30'; // set from date default value    

        <?php
        if (isset($json_data)) {
            $js_data = json_encode($json_data);
            echo " data = " . $js_data . ";\n";
        }
        ?>
        if (data) {
            $("#button_export").click(function() {
                $("#data-table").table2excel({
                    // exclude CSS class
                    exclude: ".noExl",
                    name: "Worksheet Name",
                    filename: "data", //do not include extension
                    fileext: ".xls" // file extension
                });
            });


        }

    });
</script>

<script type="module" src="js/scatterPlot.js"></script>
<?php
require_once 'includes/footer.php';
?>