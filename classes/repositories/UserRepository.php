<?php
require_once 'BaseRepository.php';
require_once __DIR__ . '/../models/Admin.php';
class UserRepository extends BaseRepository {
    protected function getTableName() {
        return 'User';
    }
    public function authenticate($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE email = ? ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE email = ? ");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function add(User $user) {
        $data = $user->toArray();
        $stmt = $this->pdo->prepare("INSERT INTO User (email, first_name, last_name, username, password, role) VALUES (?, ?, ?, ?, ?, ?) ");
        $result = $stmt->execute([
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role']
        ]);
        if ($result) {
            return $this->getLastInsertId();
        }
        return false;
    }
    public function edit($id, User $user) {
        $data = $user->toArray();
        $stmt = $this->pdo->prepare("UPDATE User SET email = ?, first_name = ?, last_name = ?, username = ?, role = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['username'],
            $data['role'],
            $id
        ]);
    }
    public function changePassword($id, $newPassword) {
        $stmt = $this->pdo->prepare("UPDATE User SET password = ? WHERE id = ? ");
        return $stmt->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            $id
        ]);
    }
}