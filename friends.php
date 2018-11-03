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
            Ismerősök
        </h1>
        <?php
            $friends = $db->getFriends($_SESSION["user"]);
            foreach ($friends as $friend) {
                $profile_picture = "assets/profile-placeholder.jpg";
                $rawData = $db->getImage($friend);
                if ($rawData != null) {
                    $data = base64_encode($rawData);
                    $profile_picture = "data:image/jpeg;base64, $data";
                }
        ?>
                <article>
                    <div class="user-data-container">
                        <img class="thumbnail" src="<?php echo $profile_picture ?>" alt="profile">
                        <p>
                            <?php echo $friend ?>
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