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
    <h>Отримати склад бригад, яка бере участь в складанні зазначеного виробу.
    </h>
</div>
<?php
$query = "SELECT distinct  assemble_id, production_title  
          FROM aviator.productions_assemble_flow
          left join productions p on p.production_id = productions_assemble_flow.production_id";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
$table_data_rows_number = mysqli_num_rows($result);

echo "
<form method = 'post'>
   <label for='production'>production: </label> 
   <select name='production' >";
for ($i = 0; $i < $table_data_rows_number; ++$i) {
    $rows = mysqli_fetch_row($result);
    echo "<option value='$rows[0]'>assemble_id = $rows[0] title = $rows[1]</option>";
}
echo "</select> ";
echo " 
       <p> <input type='submit' value='find brigada sostav'> </p>  
</form>";


if (isset($_POST["production"])

) {
    $production = htmlentities(mysqli_real_escape_string($link, $_POST['production'])) ?: 0;
    $query = "
        select production_title, assemble_id, stf.brigada_id, staff_full_name  from 
        (select distinct brigada_id, production_id, assemble_id from aviator.productions_assemble_flow) br_fl
        left join (select distinct brigada_id, staff_full_name from aviator.staff)  stf
         on stf.brigada_id = br_fl.brigada_id 
        left join productions pr on br_fl.production_id = pr.production_id
        where assemble_id = $production
         ";

    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $columns = array('production_title', 'assemble_id', 'brigada_id', 'staff_full_name');

    show_table($columns, $result, 'staff worked with the production: ');


    mysqli_close($link);
}
?>

</body>
</html>