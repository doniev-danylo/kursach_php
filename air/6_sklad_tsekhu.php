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
    <h>Отримати дані про кадровий склад цеху,
        підприємства в цілому і по зазначеним категоріям інженерно-технічного персоналу і робітників.
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
$query = "SELECT distinct staff_position FROM aviator.staff";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
$table_data_rows_number = mysqli_num_rows($result);
echo " <p>
       <label for='position'>position </label> 
       <select name='position' >
          <option value='all'>all positions</option>";
for ($i = 0; $i < $table_data_rows_number; ++$i) {
    $rows = mysqli_fetch_row($result);
    echo "<option value='$rows[0]'>$rows[0]</option>";
}
echo "        </select>
       </p> 
       <p> <input type='submit' value='show stuff list'> </p>  
</form>";


if (isset($_POST["department"])
    && isset($_POST["position"])
) {
    $department = htmlentities(mysqli_real_escape_string($link, $_POST['department'])) ?: 0;
    $position = htmlentities(mysqli_real_escape_string($link, $_POST['position']));

    if ($position == 'all') $condition = " staff_position is not null";
    else $condition = " staff_position = '$position'";

    if ($department == 0) {
        $query = " 
         select department_name, staff_full_name, staff_position, staff_seniority
         from staff left join departments d on staff.department_id = d.department_id
         where" . $condition;

    } else {
        $query = "
         select department_name,  staff_full_name,staff_position,  staff_seniority
         from staff
         left join departments d on staff.department_id = d.department_id
         where staff.department_id = $department and" . $condition;

    }
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $columns = array('prod unit', 'full name', 'position', 'seniority');
    show_table($columns, $result);

    mysqli_close($link);
}
?>

</body>
</html>