<?php
require_once 'includes/header.php';
?>
<section class="section__title">
    <h2 class="section__title--sensor">
        Image Gallery
    </h2>
</section>
<section>
    <div class="section__filter">

        <form action="images.php" method="GET">

            <div class="section__filter section__filter--date">
                <label for="start">From : </label>
                <input class="section__filter--date" type="datetime-local" id="from" name="to_date" required>

                <label for="start">To : </label>
                <input class="section__filter--date" type="datetime-local" id="to" name="from_date" required>
            </div>

            <div class="section__filter section__filter--number">
                <label for="image">Select maximum of Images : </label>
                <select id="image_number" name="image_number">
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
                <button class="section__fiter--submit image__submit" type="submit" name="submit" id="submit"> Create Image Gallery</button>
            </div>

        </form>
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


    });
</script>

<?php
if (isset($_GET['submit'])) {
    $to_date = date("Y-m-d H:i:s", strtotime($_GET['to_date']));
    $from_date = date("Y-m-d H:i:s", strtotime($_GET['from_date']));
    $row_number = $_GET['image_number'];
    $sql = "
    SELECT image_name, image_path, timestamp 
    FROM image_table
    WHERE Timestamp BETWEEN '$to_date' and '$from_date'
    ORDER BY timestamp DESC LIMIT $row_number";

    $result = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($result);

    if ($rowCount > 0) {
        echo ('<div class="section__image--gallery">');
        while ($rows = mysqli_fetch_array($result)) {
            $img_name = $rows['image_name'];
            $img_src = $rows['image_path'];
            $timestamp = $rows['timestamp'];
?>
            <div class="image__gallery">
                <a target="_blank" href=<?php echo $img_src; ?>>
                    <img src=<?php echo $img_src; ?> alt="<?php echo $img_name; ?>" title=<?php echo $img_name; ?> width="640" height="320" />
                </a>
                <div class="image__description">
                    Timestamp : <?php echo $timestamp; ?>
                </div>
            </div>
<?php
        }
        echo ('</div>');
    } else {
        echo ("No result");
    }
}
?>

<?php
require_once 'includes/footer.php';
?>