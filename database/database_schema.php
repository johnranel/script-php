<?php
    class DatabaseSchema {
        private $db_conn;

        public function __construct($db_conn) {
            $this->db_conn = $db_conn;
        }

        public function selectMySqlDatabaseIfExists(string $database_name): bool {
            $database_exists = $this->db_conn->query("SHOW DATABASES LIKE '$database_name'");
            if(mysqli_num_rows($database_exists) > 0) {
                mysqli_select_db($this->db_conn, $database_name);
                echo "Database selected.\n";
                return true;
            }

            return false;
        }

        public function createMySqlDatabase(string $database_name): bool {
            $sql_query = "CREATE DATABASE $database_name";
            if($this->db_conn->query($sql_query) === TRUE) {
                $this->selectMySqlDatabaseIfExists($database_name);
                echo "Database created successfully.\n";
                return true;
            } else {
                throw new Exception("Error occured during creation of the database.\n**\n");
            }
        }

        public function createMySqlDatabaseUserTable(): bool {
            $table_exists = $this->db_conn->query("SHOW TABLES LIKE 'users'");
            if(mysqli_num_rows($table_exists) <=0) {
                $sql = "CREATE TABLE users (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    surname VARCHAR(100) NOT NULL,
                    email VARCHAR(150) UNIQUE NOT NULL
                );";

                if($this->db_conn->query($sql) === TRUE) {
                    echo "Users table created successfully. \n**\n";
                    return true;
                } else {
                    throw new Exception("Error occured during creation of users table. \n**\n");
                }
            }
            throw new Exception("Users table already exists.\n**\n");
        }
    }
?>