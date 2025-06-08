<?php
    $options = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);

    if(count(options) <= 0)
        exit("**\nPlease enter your command line options/directives.\nUse --help command to view the options/directives.\n**\n");

    if(isset($options["create_table"]))
        createTable();

    if(isset($options["file"]))
        iterateUsersCsv();
    
    if(isset($options["help"]))
        showOptionsOrDirectives();

    function createTable() {
        // TO DO:
        // 1. Connect database
        // 2. Create database
        // 3. Create users table
        exit("connect and create database, users table functions execute.\n");
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