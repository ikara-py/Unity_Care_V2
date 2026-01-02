<?php
require_once 'User.php';

class Doctor extends User {
    private $department_id;
    private $specialty;

    public function __construct($data = []) {
        parent::__construct($data);
        $this->department_id = $data['department_id'] ?? null;
        $this->specialty = $data['specialty'] ?? '';
    }

    public function getDepartmentId() { return $this->department_id; }
    public function getSpecialty() { return $this->specialty; }
    public function setDepartmentId($department_id) { $this->department_id = $department_id; }
    public function setSpecialty($specialty) { $this->specialty = $specialty; }
}