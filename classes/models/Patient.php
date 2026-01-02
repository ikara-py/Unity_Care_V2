<?php
require_once 'User.php';

class Patient extends User {
    private $date_of_birth;
    private $phone;

    public function __construct($data = []) {
        parent::__construct($data);
        $this->date_of_birth = $data['date_of_birth'] ?? null;
        $this->phone = $data['phone'] ?? '';
    }

    public function getDateOfBirth() { return $this->date_of_birth; }
    public function getPhone() { return $this->phone; }
    public function setDateOfBirth($date_of_birth) { $this->date_of_birth = $date_of_birth; }
    public function setPhone($phone) { $this->phone = $phone; }
}