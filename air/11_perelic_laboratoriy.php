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
    <h>Отримати перелік випробувальних лабораторій,
        що беруть участь у випробуваннях деякого конкретного виробу.
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
        select production_title, responsible_person,  tests.assemble_id, laboratory_title, is_accept_product  from 
        aviator.tests
        left join (select distinct assemble_id, production_id from productions_assemble_flow) paf 
               on paf.assemble_id=tests.assemble_id
        left join productions on productions.production_id = paf.production_id 
        left join test_laboratories tl on tests.laboratory_id = tl.laboratory_id
                                                                  
        where tests.assemble_id = $production
         ";

    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    $columns = array('production_title','responsible_person', 'assemble_id', 'laboratory_title', 'is_accept_product' );

    show_table($columns, $result, 'laboratories tested the production: ');


    mysqli_close($link);
}
?>

</body>
</html>