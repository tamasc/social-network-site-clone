<!DOCTYPE html>
<html>
<head>
    <title>Facebook</title>
    <link rel="stylesheet" href="css/main.css" type="text/css">
    <link rel="stylesheet" href="css/print.css" type="text/css" media="print">
</head>
<body>
<header>
    <img class="main-logo" src="assets/facebook-app-logo.svg" alt="facebook logo">
    <a class="profile" href="
        <?php
            if (isset($_SESSION["user"])) {
                echo "profile.php";
            } else {
                echo "login.php";
            }
        ?>
    ">
        <div>
            <?php
                $profile_picture = "assets/profile-placeholder.jpg";
                if (isset($_SESSION["user"])) {
                    $profile_picture = "img/" .  $_SESSION["user"] . ".jpg";
                    echo $_SESSION["user"];
                }
            ?>
        </div>
        <img src="<?php echo $profile_picture ?>" alt="profile picture placeholder">
    </a>
</header>
<nav>
    <ul>
        <?php
            if (isset($_SESSION["user"])) {
        ?>
            <li class="<?php echo ($_SERVER['PHP_SELF'] == '/board.php' ? ' active' : '');?>">
                <a  href="board.php">Hírfolyam</a>
            </li>
        <?php
           }
        ?>
        <?php
            if (!isset($_SESSION["user"])) {
        ?>
                <li class="<?php echo ($_SERVER['PHP_SELF'] == '/registration.php' ? ' active' : '');?>">
                    <a href="registration.php">Regisztráció</a>
                </li>
                <li class="<?php echo ($_SERVER['PHP_SELF'] == '/login.php' ? ' active' : '');?>">
                    <a href="login.php">Bejelentkezés</a>
                </li>
        <?php
            }
        ?>
        <li class="<?php echo ($_SERVER['PHP_SELF'] == '/about.php' ? ' active' : '');?>">
            <a href="about.php">Rólunk</a>
        </li>
        <?php
            if (isset($_SESSION["user"])) {
        ?>      <li class="<?php echo ($_SERVER['PHP_SELF'] == '/profile.php' ? ' active' : '');?>">
                    <a href="profile.php">Profil</a>
                </li>
                <li class="logout <?php echo ($_SERVER['PHP_SELF'] == '/logout.php' ? ' active' : '');?>">
                    <a href="logout.php">Kijelentkezés</a>
                </li>
        <?php
            }
        ?>
    </ul>
</nav>