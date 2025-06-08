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
        // TO DO:
        // 1. Show options
        exit("show list of options/directives here.\n");
    }
?>