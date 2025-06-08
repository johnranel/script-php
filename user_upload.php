<?php
    $options = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);

    if(count($options) <= 0)
        exit("**\nPlease enter your command line options/directives.\nUse --help command to view the options/directives.\n**\n");

    if(isset($options["create_table"]))
        createTable($options);

    if(isset($options["file"]))
        iterateUsersCsv();
    
    if(isset($options["help"]))
        showOptionsOrDirectives();

    function createTable($options) {
        echo "**\nProcessing creation of database and user table...\n";
        $mysql_parameters_arr = mySqlParametersCheck($options);
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

    function iterateUsersCsv() {
        // TO DO:
        // 1. Capture file name
        // 2. Loop through CSV file
        // 3. Save CSV data on to the database
        exit("capture file, loop through csv and save to database.\n");
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