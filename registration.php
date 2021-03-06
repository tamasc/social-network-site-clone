<?php
function runValidation() {
    if (isset($_POST["username"]) &&
        isset($_POST["password"]) &&
        isset($_POST["password_confirm"]) &&
        isset($_FILES["profile-picture"])
        ) {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);
        $password_confirm = trim($_POST["password_confirm"]);

        if (strlen($username) < 3) {
            echo "<p class=\"error\">A felhasználónév hossza min. 3 karakter!</p>";
            echo "<br>";
            return;
        }
        if (strlen($password) < 6) {
            echo "<p class=\"error\">A jelszó hossza min. 6 karakter!</p>";
            echo "<br>";
            return;
        }
        $db = new DBConnection();
        $felhasznalok = $db->getUsers();
        if (isset($felhasznalok[$username])) {
            echo "<p class=\"error\">Ilyen nevű felhasználó már létezik!</p>";
            echo "<br>";
            return;
        } elseif(!($password_confirm == $password)) {
            echo "<p class=\"error\">A megadott jelszavak nem egyeznek!</p>";
            echo "<br>";
            return;
        }
        $filename = $_FILES["profile-picture"]["name"];
        $tempfile = $_FILES["profile-picture"]["tmp_name"];
        $data = addslashes(file_get_contents($tempfile));
        $ext = pathinfo(basename($filename), PATHINFO_EXTENSION);
        if(!in_array($ext,  array('jpeg' ,'jpg'))) {
            echo "<p class=\"error\">Csak jpg és jpeg formátumú kép tölthető fel!</p>";
            return;
        }
        $db->insertUser($username, $password);
        $db->insertPicture($filename, $data, $username);
        header('Location: login.php');
        return;
    }
}
session_start();
if (isset($_SESSION["user"])) {
    header('Location: board.php');
    die();
}
include("fejlec.php");
?>
    <main class="registration">
        <section>
            <h1>
                Regisztráció
            </h1>
                <form action="" method="post" enctype="multipart/form-data">
                    <label class="required" for="username">Felhasználónév</label>
                    <input type="text" required name="username">
                    <label class="required" for="password">Jelszó</label>
                    <input type="password" required name="password">
                    <label class="required" for="password-confirm">Jelszó megerősítése</label>
                    <input type="password" required name="password_confirm">
                    <label class="required" for="profile-picture">Profilkép</label>
                    <input type="file" required name="profile-picture" accept=".jpg,.jpeg">
                    <input class="blue-button" type="submit" value="Regisztráció">
                </form>
                <?php
                    runValidation();
                ?>
        </section>
    </main>
<?php
    include("lablec.php")
?>
