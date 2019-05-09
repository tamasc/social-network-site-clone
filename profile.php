<?php
session_start();
include("fejlec.php");
if (!isset($_SESSION["user"])) {
    session_destroy();
    header('Location: login.php');
    die();
}
$profile_picture = "assets/profile-placeholder.jpg";
$rawData = $db->getImage($_SESSION["user"]);
if ($rawData != null) {
    $data = base64_encode($rawData);
    $profile_picture = "data:image/jpeg;base64, $data";
}
?>
    <main class="modify-profile flex-middle">
        <section>
            <h1>
                Profil szerkesztése
            </h1>
            <img src="<?php echo $profile_picture ?>" alt="profilkép">
            <form class="flex-middle" action="" method="post" enctype="multipart/form-data">
                <label class="required" for="profile-picture">Profilkép módosítása</label>
                <input type="file" required name="profile-picture" accept=".jpg,.jpeg">
                <input class="blue-button" type="submit" value="Feltöltés">
            </form>
            <?php
            if (isset($_FILES["profile-picture"])) {
                $filename = $_FILES["profile-picture"]["name"];
                $tempfile = $_FILES["profile-picture"]["tmp_name"];
                $data = file_get_contents($tempfile);
                // $data = addslashes(file_get_contents($tempfile));
                $ext = pathinfo(basename($filename), PATHINFO_EXTENSION);
                if(!in_array($ext,  array('jpeg' ,'jpg'))) {
                    echo "<p class=\"error\">Csak jpg és jpeg formátumú kép tölthető fel!</p>";
                    die();
                }
                $db->updatePicture($_SESSION["user"], $filename, $data);
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
