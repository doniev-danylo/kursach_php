<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <?php
    session_start();
    $is_user_auth = $_SESSION['loggedin'];
    if ($is_user_auth != 1) header("Location: index.php");
    ?>
</head>
<body>

<div class="center-screen">
    <img src="Antonov-Logo.wine.png" width="200" height="100">
</div>

<div class="grid-container">
    <button class="block" onclick="location.href='1_perelick_viriobiv.php'">
        1 - Отримати перелік видів виробів окремої категорії і в цілому,
        що збираються зазначеним цехом або підприємством
    </button>
    <button class="block" onclick="location.href='2_perelick_za_chas.php'">
        2 - Отримати число і перелік виробів окремої категорії і в цілому,
        зібраних зазначеним цехом, ділянкою, підприємством в
        цілому за певний відрізок часу.
    </button>
    <button class="block" onclick="location.href='3_perelick_now.php'">
        3 - Отримати число і перелік виробів окремої категорії і в цілому,
        що збираються зазначеним цехом, ділянкою, підприємством в цілому в даний час.
    </button>
    <button class="block" onclick="location.href='3_perelick_now.php'">
        4 - Отримати перелік виробів окремої категорії і в цілому,
        що збираються зараз зазначеною ділянкою, цехом, підприємством.
    </button>
    <button class="block" onclick="location.href='5_perelic_robit_virib.php'">
        5 - Отримати перелік робіт, які проходить вказаний виріб.
    </button>

    <!--//////////////////////////////////////////////////////////////////////////////////////////-->

    <button class="block" onclick="location.href='6_sklad_tsekhu.php'">
        6 - Отримати дані про кадровий склад цеху, підприємства в цілому і по зазначеним категоріям інженерно-технічного
        персоналу і робітників.
    </button>
    <button class="block" onclick="location.href='7_dilyanki_tsekhu.php'">
        7 - Отримати число і перелік ділянок зазначеного цеху,
        підприємства в цілому та їх начальників.
    </button>
    <button class="block" onclick="location.href='8_sklad_brigad.php'">
        8 - Отримати склад бригад зазначеної ділянки, цеху.
    </button>
    <button class="block" onclick="location.href='9_sklad_miastery.php'">
        9 - Отримати список майстрів вказаної ділянки, цеху.
    </button>
    <button class="block" onclick="location.href='10_brigady_virobnitstvo.php'">
        10 - Отримати склад бригад, яка бере участь в складанні зазначеного виробу.
    </button>

    <!--//////////////////////////////////////////////////////////////////////////////////////////-->

    <button class="block" onclick="location.href='11_perelic_laboratoriy.php'">
        11 - Отримати перелік випробувальних лабораторій,
        що беруть участь у випробуваннях деякого конкретного виробу.
    </button>
    <button class="block" onclick="location.href='12_viprobuvannia_za_period.php'">
        12 - Отримати перелік виробів окремої категорії і в цілому,
        що проходили випробування у зазначеній лабораторії за певний період.
    </button>
    <button class="block" onclick="location.href='13_spisok_viprobuvachiv.php'">
        13 - Отримати список випробувачів, що беруть участь у випробуваннях зазначеного виробу,
        виробів окремої категорії і в цілому в деякій лабораторії за певний період.
    </button>
    <button class="block" onclick="location.href='14_spisok_obladnannia.php'">
        14 - Отримати склад обладнання, що використовувалося при випробуванні зазначеного виробу,
        виробів окремої категорії і
        в цілому в деякій лабораторії за певний період.
    </button>
</div>

</body>
</html>