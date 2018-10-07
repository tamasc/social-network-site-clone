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
                $hirek_file = fopen("hirek", "a") or die("nem lehet a fájlt megnyitni");
                $news_data = $_SESSION["user"] . "|" . $_POST["news"];
                fwrite($hirek_file, "\n". $news_data);
                fclose($hirek_file);
                unset($_POST);
                header("Location: " . $_SERVER['PHP_SELF']);
            }
            $hirek = array_reverse(file("hirek"));
            foreach ($hirek as $hir) {
                $hirTomb = explode("|", $hir);
                $thumbnail_url = "img/" . $hirTomb[0] . ".jpg";
        ?>
                <article>
                    <div class="user-data-container">
                        <img class="thumbnail" src="<?php echo $thumbnail_url ?>" alt="profile">
                        <p>
                            <?php echo $hirTomb[0] ?>
                        </p>
                    </div>
                    <p>
                        <?php echo $hirTomb[1] ?>
                    </p>
                </article>
        <?php
            }
        ?>
    </main>
<?php
    include("lablec.php")
?>