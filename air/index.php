<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../styles.css">
    <link rel="icon" type="image/x-icon" href="favicon.png">
</head>
<body>
<div class="center-screen">
    <img src="Antonov-Logo.wine.png" width="200" height="100">
</div>
<div class="center-screen">
    <form method='POST'>
        <p><b>log in</b></p>
        <p><input placeholder='login' type='text' name='login'/></p>
        <p><input placeholder='password' type='text' name='password'/></p>
        <input type='submit' value='sign in'>
    </form>
    <?php
    session_start();
    require_once 'functions.php';
    $link = link_to_db();
    $_SESSION['loggedin'] = 0;

    if (isset($_POST['login'])
        && isset($_POST['password'])
    ) {
        $login = htmlentities(mysqli_real_escape_string($link, $_POST['login']));
        $password = htmlentities(mysqli_real_escape_string($link, $_POST['password']));

        if (strlen($password) > 4) {
            $query = "SELECT password FROM `0_passwords` where login = '$login' ";
            $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));


            if ($result) {
                $password_hash = mysqli_fetch_row($result)[0];
                if (password_verify($password, $password_hash)) {
                    $_SESSION['loggedin'] = 1;
                    header("Location: main.php");

                } else echo "<script>alert('bad credentials, try again')</script>";
            }
        } else echo "<script>alert('password length should be > 4')</script>";

    }


    ?>
</div>
</body>
</html>

