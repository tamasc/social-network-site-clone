<?php
session_start();
include("fejlec.php");
?>
    <main class="modify-profile flex-middle">
        <section>
            <h1>
                Profil szerkesztése
            </h1>
            <img src="img/<?php echo $_SESSION['user'] . '.jpg' ?>" alt="profilkép">
            <form class="flex-middle" action="" method="post" enctype="multipart/form-data">
                <label class="required" for="profile-picture">Profilkép módosítása</label>
                <input type="file" required name="profile-picture" accept=".jpg,.jpeg">
                <input class="blue-button" type="submit" value="Feltöltés">
            </form>
            <?php
            if (isset($_FILES["profile-picture"])) {
                $filename = $_FILES["profile-picture"]["name"];
                $tempfile = $_FILES["profile-picture"]["tmp_name"];
                $ext = pathinfo(basename($filename), PATHINFO_EXTENSION);
                if(!in_array($ext,  array('jpeg' ,'jpg'))) {
                    echo "<p class=\"error\">Csak jpg és jpeg formátumú kép tölthető fel!</p>";
                    die();
                }
                move_uploaded_file($tempfile, "img/" . $_SESSION['user'] . ".jpg");
                unset($_POST);
                unset($_FILES);
                header("Location: " . $_SERVER['PHP_SELF']);
            }
                ?>
        </section>
    </main>
<?php
    include("lablec.php")
?>
