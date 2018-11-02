<?php
    include("./helpers/db-connection.php");
    $db = new DBConnection();
    ob_start();
?>
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
            $user = isset($_SESSION["user"]) ? $_SESSION["user"] : false;
            if ($user) {
                echo "profile.php";
            } else {
                echo "login.php";
            }
        ?>
    ">
        <?php
            $profile_picture = "assets/profile-placeholder.jpg";
            $alt = $user ? 'profile picture' : 'profile picture placeholder';
            if ($user) {
                $rawData = $db->getImage($user);
                if ($rawData != null) {
                    $data = base64_encode($rawData);
                    $profile_picture = "data:image/jpeg;base64, $data";
                }
                echo "<div>$user</div>";
            }
            echo "<img src=\"$profile_picture\" alt=\"$alt\">";
        ?>
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