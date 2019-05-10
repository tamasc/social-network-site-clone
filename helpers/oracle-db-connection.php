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
            $sqlInsertPicture = "DECLARE idp NUMBER; filep BLOB; BEGIN " .
            "INSERT INTO pictures (name, file_blob) VALUES ('$fileName', EMPTY_BLOB()) returning id, file_blob into :idp, :filep; END;";
            $statementInsert = oci_parse($connection, $sqlInsertPicture) or die ('Hibás utasítás a kép elmentésénél!');
            $blob = oci_new_descriptor($connection, OCI_D_LOB);
            oci_bind_by_name($statementInsert, "filep", $blob, -1, OCI_B_BLOB);
            oci_bind_by_name($statementInsert, "idp", $pictureId);
            oci_execute($statementInsert, OCI_NO_AUTO_COMMIT);
            if (!$blob->save($data)) {
                oci_rollback($connection);
            } else {
                oci_commit($connection);
            }
            $sqlPicUserConn = "INSERT INTO pictureowners (pictureid, userName) VALUES ($pictureId, '$owner') returning pictureid into :id";
            $statementPicUserConn = oci_parse($connection, $sqlPicUserConn) or die ('Hibás utasítás a kép - felhasználó kapcsolat létrehozásánál!');
            oci_bind_by_name($statementPicUserConn, ":id", $insertId);
            oci_execute($statementPicUserConn);
            oci_free_statement($statementInsert);
            oci_free_statement($statementPicUserConn);
            $blob->free();
            oci_close($connection);
            return $insertId;
        }

        public function getImage($user) {
            $sql = "SELECT file_blob FROM pictures JOIN pictureowners ON id=pictureid WHERE username='$user'";
            $res = $this->getSimpleQuerries($sql);
            $imageArray = oci_fetch_array($res, OCI_ASSOC);
            if (!$imageArray['FILE_BLOB']) {
                return null;
            }
            $image = ($imageArray['FILE_BLOB']->load());
            return $image;
        }

        public function updatePicture($user, $pictureName, $data) {
            $connection = $this->getConnection();
            $sql = "DECLARE filep BLOB; BEGIN " .
            "UPDATE pictures SET name='$pictureName', file_blob=EMPTY_BLOB() WHERE id=(SELECT pictureid FROM pictureowners WHERE username='$user') ".
            "returning file_blob into :filep; END;";
            $statement = oci_parse($connection, $sql) or die ('Hibás utasítás a kép elmentésénél!');
            $blob = oci_new_descriptor($connection, OCI_D_LOB);
            oci_bind_by_name($statement, "filep", $blob, -1, OCI_B_BLOB);
            oci_execute($statement, OCI_NO_AUTO_COMMIT);
            if (!$blob->save($data)) {
                oci_rollback($connection);
            } else {
                oci_commit($connection);
            }
            oci_free_statement($statement);
            $blob->free();
            oci_close($connection);
        }

        public function insertNews($user, $newsText) {
            $sql = "INSERT INTO news (user_name, text) VALUES ('$user', '$newsText')";
            $this->getSimpleQuerries($sql);
        }

        public function getNews($user) {
            $sql = "SELECT * FROM news WHERE user_name='$user' OR user_name IN (SELECT user1 FROM relations WHERE user2='$user') OR user_name IN (SELECT user2 FROM relations WHERE user1='$user')";
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

        public function getFriendNumber($user) {
            $sql = "SELECT COUNT(*) AS NUM FROM (SELECT username FROM users WHERE username IN (SELECT DISTINCT user2 FROM relations WHERE user1='$user') OR username IN (SELECT DISTINCT user1 FROM relations WHERE user2='$user'))";
            return $this->getSimpleQuerries($sql);
        }

        public function getFriends($user) {
            $sql = "SELECT username FROM users WHERE username IN (SELECT DISTINCT user2 FROM relations WHERE user1='$user') OR username IN (SELECT DISTINCT user1 FROM relations WHERE user2='$user')";
            return $this->getArrayLikeQueries($sql, 'USERNAME');
        }

        public function getOtherPeople($user) {
            $sql = "SELECT username FROM users WHERE username<>'$user' AND username NOT IN (SELECT DISTINCT user2 FROM relations WHERE user1='$user') AND username NOT IN (SELECT DISTINCT user1 FROM relations WHERE user2='$user')";
            return $this->getArrayLikeQueries($sql, 'USERNAME');
        }

        public function insertComment($commentId, $user, $text) {
            $sql = "INSERT INTO COMMENTS(NEWS_ID, USER_ID, TEXT) VALUES('$commentId', '$user', '$text')";
            $this->getSimpleQuerries($sql);
        }

        public function getComments($newsId) {
            $sql = "SELECT USER_ID, TEXT FROM COMMENTS WHERE NEWS_ID='$newsId'";
            return $this->getArrayLikeQueries($sql);
        }

        private function getSimpleQuerries($sql) {
            $connection = $this->getConnection();
            $statement = oci_parse($connection, $sql) or die ('Hibás utasítás!');
            oci_execute($statement);
            oci_commit($connection);
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