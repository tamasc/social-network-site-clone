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
                $hirek_file = fopen("hirek", "a") or die("nem lehet a fájlt megnyitni");
                $news_data = $_SESSION["user"] . "|" . $_POST["news"];
                fwrite($hirek_file, "\n". $news_data);
                fclose($hirek_file);
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
                </article>
        <?php
            }
        ?>
    </main>
<?php
    include("lablec.php")
?>