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
    <h>Отримати число і перелік виробів окремої категорії і в цілому, зібраних зазначеним цехом, ділянкою,
        підприємством в
        цілому за певний відрізок часу.
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

    $prod_or_cat = htmlentities(mysqli_real_escape_string($link, $_POST['prod_or_cat']));
    $scale = htmlentities(mysqli_real_escape_string($link, $_POST['scale']));

    if ($prod_or_cat == 'class') {
        if ($scale == 'all') {
            $query = " 
             select   'all factory',  production_category , count(distinct assemble_id) 
                    from productions_assemble_flow 
                    left join productions p on productions_assemble_flow.production_id = p.production_id
             where date(timestamp) between date('$time_from') and date ('$time_to')
             group by 1,2
             ";

        } elseif ($scale == 'department') {
            $query = " 
             select distinct department_name, production_category  , count(distinct assemble_id) 
             from productions_assemble_flow paf
                 left join productions p on paf.production_id = p.production_id
                 left join department_areas da on paf.area_id = da.area_id
                 left join departments d on da.department_id = d.department_id
             where date(timestamp) between date('$time_from') and date ('$time_to')
               and d.department_id = '$scale_selector'
                 
             group by 1,2
             ";

        } elseif ($scale == 'area') {
            $query = " 
             select  distinct concat(department_name, ' ', area_name), production_category   , count(distinct assemble_id) 
             from productions_assemble_flow paf
                 left join department_areas da on paf.area_id = da.area_id
                 left join departments d on da.department_id = d.department_id
                left join productions p on paf.production_id = p.production_id
             where date(timestamp) between date('$time_from') and date ('$time_to')
                   and paf.area_id = '$scale_selector'
             group by 1,2
             ";

        }
        $columns = array('prod unit', 'segment', 'amount');
    } else {

        if ($scale == 'all') {
            $query = " 
             select   'all factory',  production_category  , production_title, count(distinct  assemble_id) 
                    from productions_assemble_flow 
                    left join productions p on productions_assemble_flow.production_id = p.production_id
             where date(timestamp) between date('$time_from') and date ('$time_to')
             group by 1,2,3
             ";

        } elseif ($scale == 'department') {
            $query = " 
             select   department_name, production_category   , production_title, count(distinct assemble_id) 
             from productions_assemble_flow paf
                 left join productions p on paf.production_id = p.production_id
                 left join department_areas da on paf.area_id = da.area_id
                 left join departments d on da.department_id = d.department_id
             where date(timestamp) between date('$time_from') and date ('$time_to')
                 and d.department_id = '$scale_selector'
             group by 1,2,3
             ";

        } elseif ($scale == 'area') {
            $query = " 
             select    concat(department_name, ' ', area_name), production_category   , production_title , count(distinct assemble_id) 
             from productions_assemble_flow paf
                 left join department_areas da on paf.area_id = da.area_id
                 left join departments d on da.department_id = d.department_id
                left join productions p on paf.production_id = p.production_id
             where date(timestamp) between date('$time_from') and date ('$time_to')
                 
              and paf.area_id = '$scale_selector'
             group by 1,2,3
             ";

        }
        $columns = array('prod unit', 'segment', 'title', 'amount');
    }
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    show_table($columns, $result);
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    echo "<a href='$url'>try one nore time</a>";

} else {

    if (!isset($_POST["scale"])
    ) {
        echo "
<form method='post'>
    <label for='scale'>select part of the whole company to find out assembled products:
        <p><select name='scale'>
                <option value='all'>all departments</option>
                <option value='department'>exact department</option>
                <option value='area'>area of department</option>
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
        if ($scale == 'department') {
            $query = "SELECT department_id, department_name  FROM aviator.departments order by department_id";
            $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
            $table_data_rows_number = mysqli_num_rows($result);

            echo " 
               <label for='scale_selector'>department: </label> 
               <select name='scale_selector' >";
            for ($i = 0; $i < $table_data_rows_number; ++$i) {
                $rows = mysqli_fetch_row($result);
                echo "<option value='$rows[0]'>$rows[1]</option>";
            }
            echo "</select> ";

        } elseif ($scale == 'area') {

            $query = "SELECT area_id, concat(department_name, ' ', area_name)  
        FROM aviator.department_areas
        left join departments d on d.department_id = department_areas.department_id";
            $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
            $table_data_rows_number = mysqli_num_rows($result);

            echo "
           
               <label for='scale_selector'>area: </label> 
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
      <p>
       <label for='prod_or_cat'>show all products or categories </label> 
       <select name='prod_or_cat' >
          <option value='all'>all production</option>
          <option value='class'>just categories</option>
       </select>
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