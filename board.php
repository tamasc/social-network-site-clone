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
        <?php
            echo "Hello " . $_SESSION["user"];
        ?>
        </h1>
        <form class="news-form" action="" method="post">
            <textarea required name="news" id="" cols="30" rows="10"></textarea>
            <input class="blue-button" type="submit" value="Hír megosztása">
        </form>
        <?php
            if (isset($_POST["news"])) {
                $db->insertNews($_SESSION["user"], $_POST["news"]);
                unset($_POST);
            }
            $hirek = array_reverse($db->getNews($_SESSION["user"]));
            foreach ($hirek as $hir) {
                $profile_picture = "assets/profile-placeholder.jpg";
                $rawData = $db->getImage($hir['USER_NAME']);
                if ($rawData != null) {
                    $data = base64_encode($rawData);
                    $profile_picture = "data:image/jpeg;base64, $data";
                }
                $hirId = $hir["ID"];
        ?>
                <article>
                    <div class="user-data-container user-data-container-border">
                        <div class="user-data-wrapper">
                            <img class="thumbnail" src="<?php echo $profile_picture ?>" alt="profile">
                            <p>
                                <?php echo $hir['USER_NAME'] ?>
                            </p>
                        </div>
                    </div>
                    <p>
                        <?php echo $hir['TEXT'] ?>
                    </p>
                    <div class="comment-wrapper">
                <?php
                    if (isset($_POST["comment"])) {
                        $db->insertComment($hirId, $_SESSION["user"], $_POST["comment"]);
                        unset($_POST);
                    }
                    $comments = $db->getComments($hirId);
                    if ($comments) {
                        $comments = array_reverse($comments);
                    }
                    foreach ($comments as $comment) {
                        $comment_profile_picture = "assets/profile-placeholder.jpg";
                        $rawData = $db->getImage($comment['USER_ID']);
                        if ($rawData != null) {
                            $data = base64_encode($rawData);
                            $comment_profile_picture = "data:image/jpeg;base64, $data";
                        }
                    ?>
                        <div class="user-data-wrapper comment-wrapper">
                            <img class="thumbnail" src="<?php echo $comment_profile_picture ?>" alt="profile">
                            <p class="user-name">
                                <?php echo $comment['USER_ID'] ?>
                            </p>
                            <p>
                                <?php echo $comment['TEXT'] ?>
                            </p>
                        </div>
                <?php
                    }
                ?>
                    <form class="comment-form" action="" method="post">
                        <textarea required name="comment" cols="25" rows="5"></textarea>
                        <input class="blue-button" type="submit" value="Komment">
                    </form>
                    </div>
                </article>
        <?php
            }
        ?>
    </main>
<?php
    include("lablec.php")
?>