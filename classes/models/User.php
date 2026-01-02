<?php
abstract class User {
    protected $id;
    protected $email;
    protected $first_name;
    protected $last_name;
    protected $username;
    protected $password;
    protected $role;
    protected $created_at;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->email = $data['email'] ?? '';
        $this->first_name = $data['first_name'] ?? '';
        $this->last_name = $data['last_name'] ?? '';
        $this->username = $data['username'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->role = $data['role'] ?? '';
        $this->created_at = $data['created_at'] ?? null;
    }

    public function getId() { return $this->id; }
    public function getEmail() { return $this->email; }
    public function getFirstName() { return $this->first_name; }
    public function getLastName() { return $this->last_name; }
    public function getUsername() { return $this->username; }
    public function getPassword() { return $this->password; }
    public function getRole() { return $this->role; }
    public function getCreatedAt() { return $this->created_at; }

    public function setId($id) { $this->id = $id; }
    public function setEmail($email) { $this->email = $email; }
    public function setFirstName($first_name) { $this->first_name = $first_name; }
    public function setLastName($last_name) { $this->last_name = $last_name; }
    public function setUsername($username) { $this->username = $username; }
    public function setPassword($password) { $this->password = $password; }
    public function setRole($role) { $this->role = $role; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }

    public function getFullName() {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function isAdmin() { return $this->role === 'admin'; }
    public function isDoctor() { return $this->role === 'doctor'; }
    public function isPatient() { return $this->role === 'patient'; }
}