<?php
require_once 'BaseRepository.php';
include_once __DIR__ . '/../models/Doctor.php';
class DoctorRepository extends BaseRepository {
    protected function getTableName() {
        return 'doctors';
    }
    public function find($id) {
        $stmt = $this->pdo->prepare("
            SELECT User.*, doctors.department_id,  doctors.specialty, departments.name as department_name, departments.location as department_location
            FROM doctors
            INNER JOIN User ON doctors.id = User.id
            INNER JOIN departments ON doctors.department_id = departments.id
            WHERE doctors.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function findAll() {
        $stmt = $this->pdo->query("
            SELECT User.*, doctors.department_id,  doctors.specialty, departments.name as department_name
            FROM doctors
            INNER JOIN User ON doctors.id = User.id
            INNER JOIN departments ON doctors.department_id = departments.id
            ORDER BY User.last_name, User.first_name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function add(Doctor $doctor) {
        $data = $doctor->toArray();
        if (!isset($data['id']) || empty($data['id'])) {
            throw new Exception("Cannot add doctor: User ID is missing in the object.");
        }
        $stmt = $this->pdo->prepare(" INSERT INTO doctors (id, department_id, specialty) VALUES (?, ?, ?) ");
        return $stmt->execute([
            $data['id'],
            $data['department_id'],
            $data['specialty']
        ]);
    }
    public function edit($id, Doctor $doctor) {
        $data = $doctor->toArray();
        $stmt = $this->pdo->prepare("UPDATE doctors SET department_id = ?, specialty = ? WHERE id = ? ");
        return $stmt->execute([
            $data['department_id'],
            $data['specialty'],
            $id
        ]);
    }
}