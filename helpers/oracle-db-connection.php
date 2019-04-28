<?php
    class DBConnection
    {
        private $uname = "system";
        private $pwd = "admin";
        private $connection_name = "localhost/XE";

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
            $statement = oci_parse($connection, $sql) or die ('Hibás utasítás!');
            oci_execute($statement);
            oci_close($connection);
            $usersArray = array();
            while (($row = oci_fetch_assoc($statement)) != null) {
                $usersArray[$row['USERNAME']] = $row['PASSWORD'];
            }
            return $usersArray;
        }

        public function getPassword($user) {
            $sql = "SELECT password FROM users WHERE username='" . $user . "'";
            $res = $this->getSimpleQuerries($sql);
            $passwordArray = array();
            while (($row = oci_fetch_assoc($res))!= null) {
                $passwordArray[] = $row;
            }
            if (!isset($passwordArray[0])) {
                return null;
            }
            return $passwordArray[0]['PASSWORD'];
        }

        public function insertUser($userName, $password) {
            $connection = $this->getConnection();
            $sql = "INSERT INTO users VALUES ('$userName', '$password')";
            $statement = oci_parse($connection, $sql) or die ('Hibás utasítás!');
            oci_execute($statement);
            oci_close($connection);
            return $userName;
        }

        public function insertPicture($fileName, $data, $owner) {
            $connection = $this->getConnection();
            $sqlInsertPicture = "INSERT INTO pictures (name, file) VALUES ('$fileName', EMPTY_BLOB()) returning id into :id";
            $statementInsert = oci_parse($connection, $sqlInsertPicture) or die ('Hibás utasítás a kép elmentésénél!');
            $newlob = oci_new_descriptor($connection, OCI_D_LOB);
            oci_bind_by_name($statementInsert, ":photo", $newlob, -1, OCI_B_BLOB);
            oci_bind_by_name($statementInsert, ":ID", $pictureId);
            oci_execute($statementInsert);
            $sqlPicUserConn = "INSERT INTO pictureowners (pictureid, userName) VALUES ($pictureId, '$owner') returning pictureid into :id";
            $statementPicUserConn = oci_parse($connection, $sqlPicUserConn) or die ('Hibás utasítás a kép - felhasználó kapcsolat létrehozásánál!');
            oci_bind_by_name($statementPicUserConn, ":ID", $insertId);
            oci_execute($statementPicUserConn);
            oci_close($connection);
            return $insertId;
        }

        public function getImage($user) {
            $sql = "SELECT file_blob FROM pictures JOIN pictureowners ON id=pictureid WHERE username='$user'";
            $res = $this->getSimpleQuerries($sql);
            $imageArray = array();
            while (($row = oci_fetch_assoc($res))!= null) {
                $imageArray[] = $row;
            }
            if (!isset($imageArray[0])) {
                return null;
            }
            return $imageArray[0]['file'];
        }

        public function updatePicture($user, $pictureName, $data) {
            $sql = "UPDATE pictures SET name = '$pictureName', file = '$data' WHERE id=(SELECT pictureid FROM pictureowners WHERE username='$user')";
            $this->getSimpleQuerries($sql);
        }

        public function insertNews($user, $newsText) {
            $sql = "INSERT INTO news (user_name, text) VALUES ('$user', '$newsText');";
            $this->getSimpleQuerries($sql);
        }

        public function getNews($user) {
            $sql = "SELECT * FROM news WHERE user_name IN (SELECT user1 FROM relations WHERE user2='$user') OR user_name IN (SELECT user2 FROM relations WHERE user1='$user')";
            return $this->getArrayLikeQueries($sql);
        }

        public function insertRelation($user1, $user2) {
            $sql =  "INSERT INTO relations (user1, user2) VALUES ('$user1', '$user2')";
            $this->getSimpleQuerries($sql);
        }

        public function deleteRelation($user1, $user2) {
            $sql =  "DELETE FROM relations WHERE (user1='$user1' AND user2='$user2') OR (user1='$user2' AND user2='$user1')";
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
            echo $sql;
            $statement = oci_parse($connection, $sql) or die ('Hibás utasítás!');
            oci_execute($statement);
            oci_close($connection);
            return $statement;
        }

        private function getArrayLikeQueries($sql, $key = false) {
            $res = $this->getSimpleQuerries($sql);
            $array = array();
            while (($row = oci_fetch_assoc($res))!= null) {
                if ($key) {
                    $array[] = $row[$key];
                } else {
                    $array[] = $row;
                }
            }
            return $array;
        }

        private function getConnection() {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
            $connection = oci_connect($this->uname, $this->pwd, $this->connection_name) or die("Hibás csatlakozás! " . oci_error() );
            return $connection;
        }
    }
?>