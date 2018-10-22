<?php
    class DBConnection
    {
        private $host = "localhost";
        private $uname = "root";
        private $pwd = "";
        private $database = "facebook";

        public function __get($property) {
            if (property_exists($this, $property)) {
                return $this->$property;
            }
        }

        public function __set($property, $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
            return $this;
        }

        public function getUsers() {
            $connection = $this->getConnection();
            $sql = "SELECT * FROM users";
            $res = mysqli_query($connection, $sql) or die ('Hibás utasítás!');
            mysqli_close($connection);
            $usersArray = array();
            while (($row = mysqli_fetch_assoc($res))!= null) {
                $usersArray[] = $row;
            }
            return $usersArray;
        }

        public function getPassword($user) {
            $connection = $this->getConnection();
            $sql = "SELECT password FROM users WHERE username='" . $user . "';";
            $res = mysqli_query($connection, $sql) or die ('Hibás utasítás!');
            mysqli_close($connection);
            $passwordArray = array();
            while (($row = mysqli_fetch_assoc($res))!= null) {
                $passwordArray[] = $row;
            }
            if (!isset($passwordArray[0])) {
                return null;
            }
            return $passwordArray[0]['password'];
        }

        private function getConnection() {
            $connection = mysqli_connect($this->host, $this->uname, $this->pwd, $this->database) or die("Hibás csatlakozás!");
            mysqli_select_db($connection, "facebook");
            return $connection;
        }
    }
?>