<?php
require_once __DIR__ . '/../../config/connection.php';
abstract class BaseRepository {
    protected $pdo;
    public function __construct() {
        $this->pdo = Database::connect();
    }
    abstract protected function getTableName();
    public function find($id) {
        $table = $this->getTableName();
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function findAll() {
        $table = $this->getTableName();
        $stmt = $this->pdo->query("SELECT * FROM {$table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function delete($id) {
        $table = $this->getTableName();
        $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    protected function getLastInsertId(): int {
        return (int) $this->pdo->lastInsertId();
    }
}