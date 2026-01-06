<?php
require_once 'BaseRepository.php';
class DepartmentRepository extends BaseRepository {
    protected function getTableName() {
        return 'departments';
    }
    public function findWithDoctorCount($id) {
        $stmt = $this->pdo->prepare("SELECT departments.id, departments.name, departments.location, (SELECT COUNT(*) FROM doctors WHERE doctors.department_id = departments.id) as doctor_count
            FROM departments
            WHERE departments.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function findAllWithStats() {
        $stmt = $this->pdo->query("SELECT departments.id, departments.name, departments.location, (SELECT COUNT(*) FROM doctors WHERE doctors.department_id = departments.id) as doctor_count
            FROM departments
            ORDER BY departments.name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function add(Department $department) {
        $data = $department->toArray();
        $stmt = $this->pdo->prepare("INSERT INTO departments (name, location) VALUES (?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['location']
        ]);
    }
    public function edit($id, Department $department) {
        $data = $department->toArray();
        $stmt = $this->pdo->prepare("UPDATE departments SET name = ?, location = ? WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            $data['location'],
            $id
        ]);
    }
}