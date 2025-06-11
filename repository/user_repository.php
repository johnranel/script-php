<?php
    class UserRepository {
        public static function saveUserData($db_conn, $user_data) {
            $sql = "INSERT INTO users (name, surname, email) VALUES (?, ?, ?);";
            $stmt = $db_conn->prepare($sql);
            $stmt->bind_param("sss", $user_data["name"], $user_data["surname"], $user_data["email"]);
            try {
                $stmt->execute();
                echo "Inserted\n";
            } catch (Exception $e) {
                $errorMessage = [
                    1062 => "Duplicate entry"
                ];
                echo $errorMessage[$e->getCode()] . " - Skipped.\n";
            }
        }
    }
?>