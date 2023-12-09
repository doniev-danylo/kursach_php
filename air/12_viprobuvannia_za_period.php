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
    <h> Отримати перелік виробів окремої категорії і в цілому,
        що проходили випробування у зазначеній лабораторії за певний період.
    </h>
</div>

<?php


if (
    isset($_POST['time_from']) &&
    isset($_POST['time_to'])

) {

    $lab_id = htmlentities(mysqli_real_escape_string($link, $_POST['lab_id']));
    $show_all_time = htmlentities(mysqli_real_escape_string($link, $_POST['show_all_time']));
    if ($show_all_time == "Yes") {
        $time_from = '1970-01-01';
        $time_to = date('Y-m-d', time());
    } else {
        $time_from = htmlentities(mysqli_real_escape_string($link, $_POST['time_from'])) ?: date('Y-m-d', time());
        $time_to = htmlentities(mysqli_real_escape_string($link, $_POST['time_to'])) ?: date('Y-m-d', time());
    }
    $category = htmlentities(mysqli_real_escape_string($link, $_POST['category']));

    if ($category == 'all') {
        $cond = " ";
    } else   {
        $cond = " and production_category = '$category' ";
    }


    $query = " 
             select   laboratory_title, responsible_person, tests.assemble_id, production_category, production_title, date(tests.timestamp)
                    from aviator.tests
        left join (select distinct assemble_id, production_id from productions_assemble_flow) paf 
               on paf.assemble_id=tests.assemble_id
        left join productions on productions.production_id = paf.production_id 
        left join test_laboratories tl on tests.laboratory_id = tl.laboratory_id
                                                                  
        where 
            tests.timestamp between '$time_from' and '$time_to' and 
            tests.assemble_id = $lab_id ".$cond;


    $columns = array('laboratory_title', 'person tester', 'assemble_id', 'production_category', 'production_title', 'date');
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    show_table($columns, $result);

    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    echo "<a href='$url'>try one nore time</a>";

} else {

    $query = "SELECT laboratory_id, laboratory_title  
              FROM aviator.test_laboratories order by 1";
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);

    echo "<form method = 'post'> ";
    echo "   <label for='lab_id'>laboratory : </label> 
             <select name='lab_id' >";
    for ($i = 0; $i < $table_data_rows_number; ++$i) {
        $rows = mysqli_fetch_row($result);
        echo "<option value='$rows[0]'>$rows[1]</option>";
    }
    echo "</select> ";

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
      </p>  
      <p>
       <label for='category'>category: </label> 
       <select name='category' >";
    echo " <option value='all'>all categories</option>";

    $query = "SELECT distinct  production_category 
              FROM aviator.productions order by 1";
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $table_data_rows_number = mysqli_num_rows($result);
    for ($i = 0; $i < $table_data_rows_number; ++$i) {
        $rows = mysqli_fetch_row($result);
        echo "<option value='$rows[0]'>$rows[0]</option>";
    }


     echo " </select>
       </p> 
        <p> <input type='submit' value='reveal data'> </p>  
        
</form>
 ";


}
mysqli_close($link);
?>

</body>
</html>