<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
session_start();
require_once 'functions.php';
$link = link_to_db();
$is_user_auth = $_SESSION['loggedin'];
if ($is_user_auth != 1) header("Location: index.php");
?>
<div class="center-screen">
    <a href="main.php">
        <img class="animated" src="Antonov-Logo.wine.png" width="200" height="100">
    </a>
</div>
<div class="header-block">
    <h>Отримати список випробувачів, що беруть участь у випробуваннях зазначеного виробу,
        виробів окремої категорії за певний період
    </h>
</div>

<?php
if (
    isset($_POST['time_from']) &&
    isset($_POST['time_to'])

) {
    $scale_selector = htmlentities(mysqli_real_escape_string($link, $_POST['scale_selector']));
    $show_all_time = htmlentities(mysqli_real_escape_string($link, $_POST['show_all_time']));
    if ($show_all_time == "Yes") {
        $time_from = '1970-01-01';
        $time_to = date('Y-m-d', time());
    } else {
        $time_from = htmlentities(mysqli_real_escape_string($link, $_POST['time_from'])) ?: date('Y-m-d', time());
        $time_to = htmlentities(mysqli_real_escape_string($link, $_POST['time_to'])) ?: date('Y-m-d', time());
    }
    $scale = htmlentities(mysqli_real_escape_string($link, $_POST['scale']));


    if ($scale == 'lab') {
        $cond = " and tests.laboratory_id = $scale_selector";
    } elseif ($scale == 'category') {
        $cond = " and productions.production_category_id = $scale_selector ";
    } elseif ($scale == 'exact') {
        $cond = " AND productions.production_id = $scale_selector ";
    }


    $query = " 
    select laboratory_title, responsible_person, tests.assemble_id, production_category, production_title, date(tests.timestamp)
    from aviator.tests
          left join (select distinct assemble_id, production_id from productions_assemble_flow) paf 
               on paf.assemble_id=tests.assemble_id
        left join productions on productions.production_id = paf.production_id 
        left join test_laboratories tl on tests.laboratory_id = tl.laboratory_id
    
    where  tests.timestamp between '$time_from' and '$time_to' ".$cond;


    $columns = array('laboratory_title', 'person tester', 'assemble_id', 'production_category', 'production_title', 'date');
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $labl = 'responsible testers are:';
    show_table($columns, $result, $labl);


    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    echo "<a href='$url'>try one nore time</a>";

}

else {

    if (!isset($_POST["scale"])
    ) {
        echo "
<form method='post'>
    <label for='scale'>select how do you want to find tester:
        <p><select name='scale'>
                <option value='lab'>by laboratory</option>
                <option value='category'>by production category</option>
                <option value='exact'>by exact production</option>
            </select></p>
    </label>
    <p><input type='submit' value='continue '></p>
</form>";
    } else {
        $scale = htmlentities(mysqli_real_escape_string($link, $_POST['scale'])) ?: 0;
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        echo "<p>you selected $scale <a href='$url'>change it</a></p>";
/////////////////////////////////////////////////////////////////////////////////////////////////////////

        echo "<form method = 'post'>";




        if ($scale == 'lab') {
            $query = "SELECT distinct  laboratory_id, laboratory_title  FROM aviator.test_laboratories";
            $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
            $table_data_rows_number = mysqli_num_rows($result);

            echo " 
               <label for='scale_selector'>laboratory: </label> 
               <select name='scale_selector' >";
            for ($i = 0; $i < $table_data_rows_number; ++$i) {
                $rows = mysqli_fetch_row($result);
                echo "<option value='$rows[0]'>$rows[1]</option>";
            }
            echo "</select> ";

        } elseif ($scale == 'category') {

            $query = "SELECT distinct production_category_id, production_category from productions";
            $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
            $table_data_rows_number = mysqli_num_rows($result);

            echo "
           
               <label for='scale_selector'>category: </label> 
               <select name='scale_selector' >";
            for ($i = 0; $i < $table_data_rows_number; ++$i) {
                $rows = mysqli_fetch_row($result);
                echo "<option value='$rows[0]'>$rows[1]</option>";
            }
            echo "</select> ";
        }

        elseif ($scale == 'exact') {

            $query = "SELECT distinct production_id, production_title from productions";
            $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
            $table_data_rows_number = mysqli_num_rows($result);

            echo "
           
               <label for='scale_selector'>product: </label> 
               <select name='scale_selector' >";
            for ($i = 0; $i < $table_data_rows_number; ++$i) {
                $rows = mysqli_fetch_row($result);
                echo "<option value='$rows[0]'>$rows[1]</option>";
            }
            echo "</select> ";
        }



        echo " 
      <p>
         <label for='time_from'>time_from: </label> 
         <input type='date' name='time_from'>
      </p>  
      <p> 
         <label for='time_to'>time_to: </label>
         <input type='date' name='time_to'> 
         <label for='show_all_time'>show all time data: </label>
         <input type='checkbox' name='show_all_time' value='Yes' />
          <input type='hidden' name='scale' value='$scale' />
      </p>  
      <p> <input type='submit' value='reveal data'> </p>  
        
</form>
 ";


    }
}
mysqli_close($link);
?>

</body>
</html>