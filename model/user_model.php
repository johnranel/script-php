<?php
    class UserModel {
        private $name;
        private $surname;
        private $email;

        public function __construct($name, $surname, $email) {
            $this->name = $name;
            $this->surname = $surname;
            $this->email = $email;
        }

        public function cleanUserData(): array {
            $this->name = $this->removeSpecialChars($this->name);
            $this->surname = $this->removeSpecialChars($this->surname);
            $this->email = strtolower(trim($this->email));
            return ["name" => $this->name, "surname" => $this->surname, "email" => $this->email];
        }

        private function removeSpecialChars($name_or_surname): string {
            $cleaned_name_or_surname = ucfirst(strtolower(trim($name_or_surname)));
            return preg_replace("/[^A-Za-z\'\-]/", "", $cleaned_name_or_surname);
        }

        public function isValidData(): bool {
            return $this->name && $this->surname && filter_var($this->email, FILTER_VALIDATE_EMAIL);
        }

        public function isNotEmpty(): bool {
            return $this->name && $this->surname && $this->email;
        }

        public function stringUserData(): string {
            return "Name: " . $this->name . ", Surname: ". $this->surname .  ", Email: " . $this->email;
        }
    }
?>