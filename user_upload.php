<?php
    require_once("./cli/commands.php");
    require_once("./database/database_connection.php");
    require_once("./database/database_schema.php");
    require_once("./importer/csv_importer.php");

    try {
        $options = Commands::parse();
    } catch(Exception $e) {
        exit($e->getMessage());
    }

    if(isset($options["create_table"])) {
        createTable($options);
    }

    if(isset($options["file"])) {
        iterateUsersCsv($options);
    }
    
    if(isset($options["help"])) {
        showOptionsOrDirectives();
    }

    function createTable($options) {
        $dry_run = isset($options["dry_run"]);
        if($dry_run) {
            echo "[SIMULATION]\n";
            echo "Database created.\n";
            echo "Users table created successfully.\n";
            exit("**\n");
        } else {
            echo "**\nProcessing creation of database and user table...\n";
        }
        
        try {
            $mysql_params = Commands::mySqlParametersCheck($options);
            $db_conn = DatabaseConnection::connectMySql($mysql_params);
            $db_schema = new DatabaseSchema($db_conn);
            $db_selected = $db_schema->selectMySqlDatabaseIfExists($mysql_params["database"]);
            if(!$db_selected) {
                $db_schema->createMySqlDatabase($mysql_params["database"]);
            }
            $db_schema->createMySqlDatabaseUserTable();
        } catch(Exception $e) {
            exit($e->getMessage());
        }
    }

    function iterateUsersCsv($options) {
        $dry_run = isset($options["dry_run"]);
        if($dry_run) {
            echo "**\n[SIMULATION]\nProcessing data...\n";
        } else {
            echo "**\nProcessing data...\n";
        }
        try {
            $mysql_params = Commands::mySqlParametersCheck($options);
            $db_conn = DatabaseConnection::connectMySql($mysql_params);
            $db_schema = new DatabaseSchema($db_conn);
            $db_selected = $db_schema->selectMySqlDatabaseIfExists($mysql_params["database"]);
            $csv_import = new CsvImporter($options["file"]);
            $csv_import->isFileExisting();
            $csv_import->checkFileIfCsv();
            $csv_import->startUserImport($db_conn, $dry_run);
        } catch(Exception $e) {
            exit($e->getMessage());
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
        $stringCommands .= "-d - MySQL database name\n";
        $stringCommands .= "--help - which will output the above list of directives with details.\n**\n";
        exit($stringCommands);
    }
?>