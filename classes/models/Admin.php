<?php
require_once 'User.php';
class Admin extends User {
    public function __construct($data = []) {
        parent::__construct($data);
    }
}