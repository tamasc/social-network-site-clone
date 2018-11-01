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
                $usersArray[$row['username']] = $row['password'];
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

        public function insertUser($userName, $password) {
            $connection = $this->getConnection();
            $sql = "INSERT INTO users VALUES ('$userName', '$password');";
            $res = mysqli_query($connection, $sql) or die ('Hibás utasítás!');
            $insertId = $connection->insert_id;
            mysqli_close($connection);
            return $insertId;
        }

        public function insertPicture($fileName, $data, $owner) {
            $connection = $this->getConnection();
            $sqlInsertPicture = "INSERT INTO pictures (name, file) VALUES ('$fileName', '$data');";
            $resInsert = mysqli_query($connection, $sqlInsertPicture) or die ('Hibás utasítás a kép elmentésénél!');
            $pictureId = $connection->insert_id;
            $sqlPicUserConn = "INSERT INTO pictureowners (pictureid, userName) VALUES ($pictureId, '$owner');";
            echo $sqlPicUserConn;
            $resPicUserConn = mysqli_query($connection, $sqlPicUserConn) or die ('Hibás utasítás a kép - felhasználó kapcsolat létrehozásánál!');
            $insertId = $connection->insert_id;
            mysqli_close($connection);
            return $insertId;
        }

        public function getImage($user) {
            $connection = $this->getConnection();
            $sql = "SELECT file FROM pictures JOIN pictureowners ON id=pictureid WHERE username='$user';";
            $res = mysqli_query($connection, $sql) or die ('Hibás utasítás!');
            mysqli_close($connection);
            $imageArray = array();
            while (($row = mysqli_fetch_assoc($res))!= null) {
                $imageArray[] = $row;
            }
            if (!isset($imageArray[0])) {
                return null;
            }
            return $imageArray[0]['file'];
        }

        private function getConnection() {
            $connection = mysqli_connect($this->host, $this->uname, $this->pwd, $this->database) or die("Hibás csatlakozás!");
            mysqli_select_db($connection, "facebook");
            return $connection;
        }
    }
?>