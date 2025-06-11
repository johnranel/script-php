<?php
class Commands {
    public static function parse(): array {
        $options = getopt("u:p:h:d:", ["file:", "create_table", "dry_run", "help"]);
        if(empty($options)) {
            throw new Exception("**\nPlease enter your command line options/directives.\nUse --help command to view the options/directives.\n**\n");
        }

        if((isset($options["u"]) || isset($options["p"]) || isset($options["h"])) && (!isset($options["create_table"]) && !isset($options["file"]))) {
            throw new Exception("**\nPlease use the commands -u (username) -p (password) -h (host) with --create_table or --file (csv file name) option.\nUse --help command to view the options/directives.\n**\n");
        }
        return $options;
    }

    public static function mySqlParametersCheck($options): array {
        $host = $options["h"] ?? "";
        $username = $options["u"] ?? "";
        $password = $options["p"] ?? "";
        $database = $options["d"] ?? "";

        if(!$host || !$username || !$password || !$database) {
            throw new Exception("The -u (username) -p (password) -h (host) -d (database) for MySQL are required to run this command.\n**\n");
        }
        
        return compact("host", "username", "password", "database");
    }
}
?>