<?php
    require_once("./../model/user_model.php");
    require_once("./../repository/user_repository.php");

    class CsvImporter {
        private $filename;

        public function __construct(string $filename) {
            $this->filename = $filename;
        }

        public function isFileExisting(): bool {
            if(!file_exists($this->filename)) {
                throw new Exception("File not found.\n**\n");
            }
            return true;
        }

        public function checkFileIfCsv(): bool {
            $path_parts = pathinfo($this->filename);
            if($path_parts["extension"] !== "csv" && mime_content_type($this->filename) !== "text/csv") {
                throw new Exception("Please use CSV files.\n**\n");
            }
            return true;
        }

        public function startUserImport($db_conn, $dry_run) {
            if(($handle = fopen($this->filename, "r")) !== FALSE) {
                $header_data_arr = fgetcsv($handle, 0, ",", '"', "\\");
                $index = $this->getHeaderIndex($header_data_arr);

                while(($user_data = fgetcsv($handle, 0, ",", '"', "\\")) !== FALSE) {
                    if(isset($user_data[$index["name"]], $user_data[$index["surname"]], $user_data[$index["email"]])) {
                        $user_model = new UserModel($user_data[$index["name"]], $user_data[$index["surname"]], $user_data[$index["email"]]);
                        $user_data_cleaned = $user_model->cleanUserData();
                        echo $user_model->stringUserData() . "\n";
                        if($user_model->isValidData()) {
                            if(!$dry_run) {
                                UserRepository::saveUserData($db_conn, $user_data_cleaned);
                            }
                            echo "Valid\n";
                        } else {
                            echo "Invalid\n";
                        }
                        
                    } else {
                        throw new Exception("CSV data format is invalid.\n");
                    }
                }
                fclose($handle);
                exit("Processing data completed.\n**\n");
            }
        }

        private function getHeaderIndex($header_data_arr) {
            $index = [];
            foreach($header_data_arr as $i => $header_name) {
                $header_name = strtolower(trim($header_name));
                if($header_name === "name") {
                    $index["name"] = $i;
                }
                if($header_name === "surname") {
                    $index["surname"] = $i;
                }
                if($header_name === "email") {
                    $index["email"] = $i;
                }
            }

            if(!isset($index["name"], $index["surname"], $index["email"])) {
                throw new Exception("CSV is missing a column name/username/email.\n**\n");
            }

            return $index;
        }
    }
?>