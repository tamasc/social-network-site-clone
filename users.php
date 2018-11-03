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
        function addFriend(user) {
            var formData = new FormData();
            formData.append('friend', user);
            fetch('users.php', {
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
                        <div class="user-data-wrapper">
                            <img class="thumbnail" src="<?php echo $profile_picture ?>" alt="profile">
                            <p>
                                <?php echo $user ?>
                            </p>
                        </div>
                        <img class="icon" src="assets/add-friend.svg" alt="add friend icon" onclick="addFriend('<?php echo $user ?>')">
                    </div>
                </article>
        <?php
            }
            if (isset($_POST['friend'])) {
                $db->insertRelation($_SESSION["user"], $_POST['friend']);
                unset($_POST);
            }
        ?>
    </main>
<?php
    include("lablec.php")
?>