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
    <h>Отримати список майстрів вказаної ділянки, цеху.
    </h>
</div>

<?php
if (
    isset($_POST['scale_selector'])
) {
    $scale = htmlentities(mysqli_real_escape_string($link, $_POST['scale']));
    $scale_selector = htmlentities(mysqli_real_escape_string($link, $_POST['scale_selector']));


    if ($scale == 'all') {
        $cond = " ";
    } elseif ($scale == 'department') {
        $cond = " and d.department_id = $scale_selector ";
    } elseif ($scale == 'area') {
        $cond = " AND staff.area_id = $scale_selector ";
    }


    $query = " 
    select  department_name, area_name, staff_full_name, staff_position, staff_seniority
    from aviator.staff
    left join department_areas da on da.area_id = staff.area_id
    left join departments d on d.department_id = da.department_id
    where staff_seniority = 'maister' " . $cond . " order by 1,2,3";


    $columns = array('department_name', 'area_name', 'staff_full_name', 'staff_position', 'staff_seniority');
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $labl = 'staff in brigadas';

    show_table($columns, $result, $labl);

    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    echo "<a href='$url'>try one nore time</a>";

} else {

    if (!isset($_POST["scale"])
    ) {
        echo "
<form method='post'>
    <label for='scale'>select part of the whole company to find out assembled products:
        <p><select name='scale'>
               <!-- <option value='all'>all departments</option>-->
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
      <p>  <input type='hidden' name='scale' value='$scale' /> </p>  
      <p> <input type='submit' value='reveal data'> </p>  
        
</form>
 ";


    }
}
mysqli_close($link);
?>

</body>
</html>