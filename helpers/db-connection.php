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
            $sql = "SELECT password FROM users WHERE username='" . $user . "';";
            $res = $this->getSimpleQuerries($sql);
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
            $resPicUserConn = mysqli_query($connection, $sqlPicUserConn) or die ('Hibás utasítás a kép - felhasználó kapcsolat létrehozásánál!');
            $insertId = $connection->insert_id;
            mysqli_close($connection);
            return $insertId;
        }

        public function getImage($user) {
            $sql = "SELECT file FROM pictures JOIN pictureowners ON id=pictureid WHERE username='$user';";
            $res = $this->getSimpleQuerries($sql);
            $imageArray = array();
            while (($row = mysqli_fetch_assoc($res))!= null) {
                $imageArray[] = $row;
            }
            if (!isset($imageArray[0])) {
                return null;
            }
            return $imageArray[0]['file'];
        }

        public function updatePicture($user, $pictureName, $data) {
            $sql = "UPDATE pictures SET name = '$pictureName', file = '$data' WHERE id=(SELECT pictureid FROM pictureowners WHERE username='$user');";
            $this->getSimpleQuerries($sql);
        }

        public function insertNews($user, $newsText) {
            $sql = "INSERT INTO news (user_name, text) VALUES ('$user', '$newsText');";
            $this->getSimpleQuerries($sql);
        }

        public function getNews() {
            $sql = "SELECT * FROM news";
            return $this->getArrayLikeQueries($sql);
        }

        public function insertRelation($user1, $user2) {
            $sql =  "INSERT INTO relations (user1, user2) VALUES ('$user1', '$user2');";
            $this->getSimpleQuerries($sql);
        }

        public function getFriends($user) {
            $sql = "SELECT username FROM users WHERE username IN (SELECT DISTINCT user2 FROM relations WHERE user1='$user') OR username IN (SELECT DISTINCT user1 FROM relations WHERE user2='$user')";
            return $this->getArrayLikeQueries($sql, 'username');
        }

        public function getOtherPeople($user) {
            $sql = "SELECT username FROM users WHERE username<>'$user' AND username NOT IN (SELECT DISTINCT user2 FROM relations WHERE user1='$user') AND username NOT IN (SELECT DISTINCT user1 FROM relations WHERE user2='$user')";
            return $this->getArrayLikeQueries($sql, 'username');
        }

        private function getSimpleQuerries($sql) {
            $connection = $this->getConnection();
            $res = mysqli_query($connection, $sql) or die ('Hibás utasítás!');
            mysqli_close($connection);
            return $res;
        }

        private function getArrayLikeQueries($sql, $key = false) {
            $res = $this->getSimpleQuerries($sql);
            $array = array();
            while (($row = mysqli_fetch_assoc($res))!= null) {
                if ($key) {
                    $array[] = $row[$key];
                } else {
                    $array[] = $row;
                }
            }
            return $array;
        }

        private function getConnection() {
            $connection = mysqli_connect($this->host, $this->uname, $this->pwd, $this->database) or die("Hibás csatlakozás!");
            mysqli_select_db($connection, "facebook");
            return $connection;
        }
    }
?>