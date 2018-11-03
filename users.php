<?php
session_start();
if (!isset($_SESSION["user"])) {
    session_destroy();
    header('Location: login.php');
    die();
}
include("fejlec.php");
?>
    <main class="board">
        <h1>
            Ismerősök keresése
        </h1>
        <?php
            $users = $db->getOtherPeople($_SESSION["user"]);
            foreach ($users as $user) {
                $profile_picture = "assets/profile-placeholder.jpg";
                $rawData = $db->getImage($user);
                if ($rawData != null) {
                    $data = base64_encode($rawData);
                    $profile_picture = "data:image/jpeg;base64, $data";
                }
        ?>
                <article>
                    <div class="user-data-container">
                        <img class="thumbnail" src="<?php echo $profile_picture ?>" alt="profile">
                        <p>
                            <?php echo $user ?>
                        </p>
                    </div>
                </article>
        <?php
            }
        ?>
    </main>
<?php
    include("lablec.php")
?>