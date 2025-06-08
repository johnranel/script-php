<?php
    $options = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);

    if(count($options) <= 0)
        exit("**\nPlease enter your command line options/directives.\nUse --help command to view the options/directives.\n**\n");

    if(isset($options["create_table"]))
        createTable($options);

    if(isset($options["file"]))
        iterateUsersCsv($options);
    
    if(isset($options["help"]))
        showOptionsOrDirectives();

    function createTable($options) {
        echo "**\nProcessing creation of database and user table...\n";
        $dry_run = isset($options["dry_run"]);
        $mysql_parameters_arr = mySqlParametersCheck($options);
        if($dry_run) {
            echo "[SIMULATION]\n";
            echo "Database created.\n";
            echo "Users table created successfully.\n";
            exit("**\n");
        }

        $db_conn = connectMySql($mysql_parameters_arr);
        $db_selected = selectMySqlDatabaseIfExists($db_conn);

        if(!$db_selected) {
            createMySqlDatabase($db_conn);
            createMySqlDatabaseUserTable($db_conn);
        }

        echo "Database selected.\n";
        createMySqlDatabaseUserTable($db_conn);

        exit("connect and create database, users table functions execute.\n");
    }

    function mySqlParametersCheck($options) {
        $host = $options["h"] ?? "";
        $username = $options["u"] ?? "";
        $password = $options["p"] ?? "";

        if(!$host || !$username || !$password)
            exit("The -u (username) -p (password) -h (host) for MySQL are required to run this command.\n**\n");
        
        return compact("host", "username", "password");
    }

    function connectMySql($mysql_parameters_arr) {
        try {
            return @mysqli_connect($mysql_parameters_arr["host"], $mysql_parameters_arr["username"], $mysql_parameters_arr["password"]);
        } catch(Exception $e) {
            exit("Please check if host, username, and password is correct.\n**\n");
        }
    }

    function selectMySqlDatabaseIfExists($db_conn, $database_name = "catalyst") {
        $database_exists = $db_conn->query("SHOW DATABASES LIKE '$database_name'");
        if(mysqli_num_rows($database_exists) > 0)
            return mysqli_select_db($db_conn, $database_name);
        return false;
    }

    function createMySqlDatabase($db_conn, $database_name = "catalyst") {
        $sql_query = "CREATE DATABASE $database_name";
        if($db_conn->query($sql_query) === TRUE) {
            selectMySqlDatabaseIfExists($db_conn);
            echo "Database created successfully.\n";
        } else {
            exit("Error occured during creation of the database.\n**\n");
        }
    }

    function createMySqlDatabaseUserTable($db_conn) {
        $table_exists = $db_conn->query("SHOW TABLES LIKE 'users'");
        if(mysqli_num_rows($table_exists) <=0) {
            $sql = "CREATE TABLE users (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                surname VARCHAR(100) NOT NULL,
                email VARCHAR(150) UNIQUE NOT NULL
            );";

            if($db_conn->query($sql) === TRUE) {
                exit("Users table created successfully. \n**\n");
            } else {
                exit("Error occured during creation of users table. \n**\n");
            }
        }
        exit("Users table already exists.\n**\n");
    }

    function iterateUsersCsv($options) {
        echo "**\nProcessing data...\n";
        $dry_run = isset($options["dry_run"]);
        if($dry_run) {
            echo "[SIMULATION]\n";
        }
        
        $file = $options["file"] ?? "";
        $mysql_parameters_arr = mySqlParametersCheck($options);
        $db_conn = connectMySql($mysql_parameters_arr);
        $db_selected = selectMySqlDatabaseIfExists($db_conn);

        if(!file_exists($file))
            exit("File not found.\n**\n");

        $path_parts = pathinfo($file);
        if($path_parts["extension"] !== "csv")
            exit("Please use CSV files.\n**\n");

        if(($handle = fopen($file, "r")) !== FALSE) {
            $header_data_arr = fgetcsv($handle, 0, ",", '"', "\\");
            $index = getHeaderIndex($header_data_arr);

            if(!isset($index["name"], $index["surname"], $index["email"]))
                exit("CSV is missing a column name/username/email.\n**\n");

            $db_conn->begin_transaction();
            while(($user_data = fgetcsv($handle, 0, ",", '"', "\\")) !== FALSE) {
                $name = formatNameAndSurname($user_data[$index["name"]]);
                $surname = formatNameAndSurname($user_data[$index["surname"]]);
                $email = validateEmail($user_data[$index["email"]]);
                echo "name: " . $name . " surname: " . $surname . " email: " . strtolower($user_data[$index["email"]]) . "\n";
                if($name && $surname && $email) {
                    if(!$dry_run) {
                        insertUserDataIntoDatabase($db_conn, $name, $surname, $email);
                    }
                    echo "Valid";
                } else {
                    echo "SKIPPED\nInvalid";
                }
                echo "\n\n";
            }
            if(!$dry_run) {
                try {
                    $db_conn->commit();
                } catch(Exception $e) {
                    $db_conn->rollback();
                    echo "Data insert failed: " . $e->getMessage() . "\n";
                }
            }
            fclose($handle);
            exit("Processing data completed.\n**\n");
        }

        exit("capture file, loop through csv and save to database.\n");
    }

    function getHeaderIndex($header_data_arr) {
        $index = [];
        foreach($header_data_arr as $i => $header_name) {
            $header_name = strtolower(trim($header_name));
            if($header_name === "name")
                $index["name"] = $i;
            if($header_name === "surname")
                $index["surname"] = $i;
            if($header_name === "email")
                $index["email"] = $i;
        }
        return $index;
    }

    function formatNameAndSurname($name_or_surname) {
        $cleaned_name_or_surname = ucfirst(strtolower(trim($name_or_surname)));
        return preg_replace("/[^A-Za-z0-9\-]/", "", $cleaned_name_or_surname);
    }

    function validateEmail($email) {
        $clean_email = strtolower(trim($email));
        if(filter_var($clean_email, FILTER_VALIDATE_EMAIL)) {
            return $clean_email;
        }
        return false;
    }

    function insertUserDataIntoDatabase($db_conn, $name, $surname, $email) {
        $sql = "INSERT INTO users (name, surname, email) VALUES (?, ?, ?);";
        try {
            $stmt = $db_conn->prepare($sql);
            $stmt->bind_param("sss", $name, $surname, $email);
            $stmt->execute();
            echo "INSERTED \n";
        } catch(Exception $e) {
            echo $e->getMessage() . " - SKIPPED.\n";
        }
    }

    function showOptionsOrDirectives() {
        $stringCommands = "**\nList of available options or directives.\n";
        $stringCommands .= "--file [csv file name] - this is the name of the CSV to be parsed\n";
        $stringCommands .= "--create_table - this will cause the MySQL users table to be built (and no further action will be taken)\n";
        $stringCommands .= "--dry_run - this will be used with the --file directive in case we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered\n";
        $stringCommands .= "-u - MySQL username\n";
        $stringCommands .= "-p - MySQL password\n";
        $stringCommands .= "-h - MySQL host\n";
        $stringCommands .= "--help - which will output the above list of directives with details.\n**\n";
        exit($stringCommands);
    }
?>