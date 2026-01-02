<?php
class Department {
    private $id;
    private $name;
    private $location;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->location = $data['location'] ?? '';
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getLocation() { return $this->location; }

    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setLocation($location) { $this->location = $location; }
}