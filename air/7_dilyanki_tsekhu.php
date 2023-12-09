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
    <h> Отримати число і перелік ділянок зазначеного цеху,
        підприємства в цілому та їх начальників.
    </h>
</div>
<?php
$query = "SELECT department_id, department_name  FROM aviator.departments";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
$table_data_rows_number = mysqli_num_rows($result);

echo "
<form method = 'post'>
   <label for='department'>department: </label> 
   <select name='department' >";
echo "<option value=0>all departments</option>";
for ($i = 0; $i < $table_data_rows_number; ++$i) {
    $rows = mysqli_fetch_row($result);
    echo "<option value='$rows[0]'>$rows[1]</option>";
}
echo "</select> ";
echo "<p> <input type='submit' value='find what company can construct'> </p>  
</form>";


if (isset($_POST["department"])
) {
    $department = htmlentities(mysqli_real_escape_string($link, $_POST['department'])) ?: 0;

    if ($department == 0) $condition = " d.department_id is not null";
    else $condition = " d.department_id = '$department'";

    // count
    $query = " 
        select count(distinct  d.department_id)
            from  departments d 
                left join (select * from aviator.staff where staff_seniority= 'department chief')  st
                on d.department_id = st.department_id
        where" . $condition;
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $number = mysqli_fetch_row($result);
    $number = $number[0];
    echo "<h2>departments - $number</h2>";

    $query = " 
        select count(distinct department_areas.area_id)
            from department_areas  
                left join departments d on department_areas.department_id = d.department_id
                left join (select * from staff where staff_seniority = 'area_chief')  st
                on department_areas.area_id = st.area_id
        where" . $condition;
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $number = mysqli_fetch_row($result);
    $number = $number[0];
    echo "<h2>areas - $number</h2>";


    //начальники департаментов
    $query = " 
        select department_name, staff_full_name, staff_position, staff_seniority
            from  departments d 
                left join (select * from aviator.staff where staff_seniority= 'department chief')  st
                on d.department_id = st.department_id
        where" . $condition . " order by 1";
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $columns = array('department_name', 'staff_full_name', 'staff_position', 'staff_seniority');
    show_table($columns, $result, 'Chiefs of departments');

    //начальники отделов
    $query = " 
        select department_name, area_name, staff_full_name, staff_position, staff_seniority
            from department_areas  
                left join departments d on department_areas.department_id = d.department_id
                left join (select * from staff where staff_seniority = 'area_chief')  st
                on department_areas.area_id = st.area_id
        where" . $condition . " order by 1, 2";
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $columns = array('department_name', 'area_name', 'staff_full_name', 'staff_position', 'staff_seniority');
    show_table($columns, $result, 'Chiefs of department areas');


    mysqli_close($link);
}
?>

</body>
</html>