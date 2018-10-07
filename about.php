<?php
session_start();
include("fejlec.php");
?>
    <main class="about">
        <section class="table-container flex-middle">
            <h2>Changelog</h2>
            <table>
                <tr>
                    <th>Időpont</th>
                    <th>Verzió</th>
                    <th>Változások</th>
                    <th>Készítő</th>
                </tr>
                <tr>
                    <td>2018.03.11</td>
                    <td>0.1.0</td>
                    <td>alapmodulok</td>
                    <td>lajos</td>
                </tr>
                <tr>
                    <td>2018.03.12</td>
                    <td>0.1.1</td>
                    <td>regisztráció</td>
                    <td>jóska</td>
                </tr>
                <tr>
                    <td>2018.03.13</td>
                    <td>0.1.3</td>
                    <td>login</td>
                    <td>jóska</td>
                </tr>
                <tr>
                    <td>2018.03.14</td>
                    <td>0.1.4</td>
                    <td>hírfolyam</td>
                    <td>lajos</td>
                </tr>
                <tr>
                    <td>2018.03.15</td>
                    <td>0.1.5</td>
                    <td>karalábé</td>
                    <td>lajos</td>
                </tr>
            </table>
        </section>
        <section class="info-icon-container flex-middle">
            <img class="info-icon" src="assets/information.svg" alt="forgó info ikon">
        </section>
    </main>

<?php
    include("lablec.php")
?>