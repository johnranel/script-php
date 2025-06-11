<?php 
    class DatabaseConnection {
        public static function connectMySql(array $mysql_params): mysqli {
            $conn = @mysqli_connect($mysql_params["host"], $mysql_params["username"], $mysql_params["password"]);
            
            if(!$conn) {
                throw new Exception("Please check the host, username, and password if correct.");
            }
            return $conn;
        }
    }
?>