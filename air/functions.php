<?php
function link_to_db()
{
    $db_host = 'localhost';
    $db_user = 'root';
    $db_password = 'root';
    $db_db = 'aviator';
    $link = mysqli_connect($db_host, $db_user, $db_password, $db_db)
    or die("Помилка " . mysqli_error($link));
    return $link;
}

function show_table($columns, $sql_result, $caption = 'result table')
{
    $table_data_rows_number = mysqli_num_rows($sql_result);
    $table_data_columns_number = mysqli_num_fields($sql_result);

    echo "<table><caption>$caption</caption>";
    echo "<thead><tr>";
    for ($i = 0; $i < count($columns); ++$i) {
        echo "<th scope='col'>$columns[$i]</th>";
    }
    echo "</tr></thead>";
    echo "<tbody>";
    for ($i = 0; $i < $table_data_rows_number; ++$i) {
        $row = mysqli_fetch_row($sql_result);
        echo "<tr>";
        for ($j = 0; $j < $table_data_columns_number; ++$j) echo "<td>$row[$j]</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";


}