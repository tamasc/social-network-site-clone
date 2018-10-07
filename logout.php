<?php
    session_start();
    session_unset();
    session_destroy();
    include("fejlec.php");
?>
    <main class="flex-middle">
        <div class="success">
            Sikeres kijelentkezés!
        </div>
    </main>
<?php
    include("lablec.php");
?>
