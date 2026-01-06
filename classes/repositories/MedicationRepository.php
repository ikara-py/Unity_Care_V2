<?php
require_once 'BaseRepository.php';
class MedicationRepository extends BaseRepository {
    protected function getTableName() {
        return 'medications';
    }
    public function findByName($name) {
        $stmt = $this->pdo->prepare(" SELECT id, name, description FROM medications WHERE name = ? ");
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function add(Medication $medication) {
        $data = $medication->toArray();
        if ($this->findByName($data['name'])) {
            return false;
        }
        $stmt = $this->pdo->prepare(" INSERT INTO medications (name, description) VALUES (?, ?) ");
        return $stmt->execute([
            $data['name'],
            $data['description']
        ]);
    }
    public function edit($id, Medication $medication) {
        $data = $medication->toArray();
        $stmt = $this->pdo->prepare("UPDATE medications SET name = ?, description = ? WHERE id = ? ");
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $id
        ]);
    }
}