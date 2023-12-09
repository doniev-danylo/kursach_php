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
    <h>Отримати перелік робіт, які проходить вказаний виріб.
    </h>
</div>
<?php
$query = "SELECT production_id, production_title  FROM aviator.productions";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
$table_data_rows_number = mysqli_num_rows($result);

echo "
<form method = 'post'>
   <label for='production'>production: </label> 
   <select name='production' >";
for ($i = 0; $i < $table_data_rows_number; ++$i) {
    $rows = mysqli_fetch_row($result);
    echo "<option value='$rows[0]'>$rows[1]</option>";
}
echo "</select> ";
echo "  <p> <input type='submit' value='show'> </p>  
</form>";


if (isset($_POST["production"])
) {
    $production = htmlentities(mysqli_real_escape_string($link, $_POST['production'])) ?: 1;

        $query = " select production_title,status_sequence_number, status 
                   from aviator.productions_assemble_statuses  pas
                   left join productions p on pas.production_id = p.production_id
                   where  pas.production_id = $production
                   order by status_sequence_number ";
        $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
        $columns = array('product title', 'assemble sequence', 'status');

    show_table($columns, $result);


    mysqli_close($link);
}
?>

</body>
</html>