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
    <h>Отримати перелік видів виробів окремої категорії і в цілому,
        що збираються зазначеним цехом або підприємством
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
echo " <p>
       <label for='slc1'>show </label> 
       <select name='slc1' >
          <option value='all'>all production</option>
          <option value='class'>just categories</option>
       </select>
       </p> 
       <p> <input type='submit' value='find what company can construct'> </p>  
</form>";


if (isset($_POST["department"])
    && isset($_POST["slc1"])
) {
    $department = htmlentities(mysqli_real_escape_string($link, $_POST['department'])) ?: 0;
    $slc1 = htmlentities(mysqli_real_escape_string($link, $_POST['slc1']));

    if ($slc1 == 'all') {
        $query = " call aviator.what_department_can_produce_title($department) ";
        $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
        $columns = array('prod unit', 'model', 'segment');
    } else {
        $query = " call aviator.what_department_can_produce_category($department) ";
        $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
        $columns = array('prod unit', 'segment');
    }
    show_table($columns, $result);


    mysqli_close($link);
}
?>

</body>
</html>