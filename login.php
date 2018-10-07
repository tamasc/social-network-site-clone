<?php
    session_start();
    if (isset($_SESSION["user"])) {
        header('Location: board.php');
        die();
    }
    include("fejlec.php");
?>
    <main class="login">
        <section>
            <h1>
                Bejelentkezés
            </h1>
            <form action="" method="post">
                <label class="required" for="username">Felhasználónév</label>
                <input type="text" required name="username">
                <label class="required" for="password">Jelszó</label>
                <input type="password" required name="password">
                <input class="blue-button" type="submit" value="Bejelentkezés">
            </form>
            <?php
            if (isset($_POST["username"]) &&
                isset($_POST["password"])) {
                $username = trim($_POST["username"]);
                $password = trim($_POST["password"]);

                if (strlen($username) < 3) {
                    echo "<p class=\"error\">A felhasználónév hossza min. 3 karakter!</p>";
                    echo "<br>";
                    session_unset();
                    session_destroy();
                    die();
                }
                if (strlen($password) < 6) {
                    echo "<p class=\"error\">A jelszó hossza min. 6 karakter!</p>";
                    echo "<br>";
                    session_unset();
                    session_destroy();
                    die();
                }
                $felhasznalok = array();
                $adatok = file("nevek");
                foreach ($adatok as &$f) {
                    $f = trim($f, "\n\t\r");
                    $f = trim($f, " ");
                    $tomb = explode("|", $f);
                    $felhasznalok[$tomb[0]] = $tomb[1];
                }
                if (!isset($felhasznalok[$username])) {
                    echo "<p class=\"error\">A felhasználó nem létezik!</p>";
                    echo "<br>";
                    session_unset();
                    session_destroy();
                    die();
                } elseif(!($felhasznalok[$username] == $password)) {
                    echo "<p class=\"error\">Helytelen jelszó!</p>";
                    echo "<br>";
                    session_unset();
                    session_destroy();
                    die();
                }
                $_SESSION["user"] = $username;
                header('Location: board.php');
            }
            ?>
        </section>
    </main>
<?php
    include("lablec.php")
?>
