<?php
session_start();
if (!isset($_SESSION["user"])) {
    session_destroy();
    header('Location: login.php');
    die();
}
include("fejlec.php");
?>
    <script>
        function removeFriend(user) {
            var formData = new FormData();
            formData.append('friend', user);
            fetch('friends.php', {
                method: 'POST',
                credentials: 'include',
                body: formData,
            })
            .then(() => {
                window.location.reload();
            });
        }
    </script>
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
                        <div class="user-data-wrapper">
                            <img class="thumbnail" src="<?php echo $profile_picture ?>" alt="profile">
                            <p>
                                <?php echo $friend ?>
                            </p>
                        </div>
                        <img class="icon" src="assets/remove-friend.svg" alt="remove friend icon" onclick="removeFriend('<?php echo $friend ?>')">
                    </div>
                </article>
        <?php
            }
            if (isset($_POST['friend'])) {
                $db->deleteRelation($_SESSION["user"], $_POST['friend']);
                unset($_POST);
            }
        ?>
    </main>
<?php
    include("lablec.php")
?>