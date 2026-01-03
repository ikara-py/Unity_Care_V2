<?php
require_once 'BaseRepository.php';

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

    public function findAllWithStats() {
        $stmt = $this->pdo->query("
            SELECT User.*, doctors.department_id,  doctors.specialty, departments.name as department_name, (SELECT COUNT(*) FROM appointments WHERE appointments.doctor_id = doctors.id AND appointments.status = 'scheduled') upcoming_appointments
            FROM doctors
            INNER JOIN User ON doctors.id = User.id
            INNER JOIN departments ON doctors.department_id = departments.id
            ORDER BY User.last_name, User.first_name
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($data) {
        $stmt = $this->pdo->prepare(" INSERT INTO doctors (id, department_id, specialty) VALUES (?, ?, ?) ");
        
        return $stmt->execute([
            $data['id'],
            $data['department_id'],
            $data['specialty']
        ]);
    }
    public function edit($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE doctors SET department_id = ?, specialty = ? WHERE id = ? ");
        return $stmt->execute([
            $data['department_id'],
            $data['specialty'],
            $id
        ]);
    }

    public function findAppointments($doctorId) {
        $stmt = $this->pdo->prepare("
            SELECT appointments.*, User.first_name as patient_first_name, User.last_name as patient_last_name
            FROM appointments
            INNER JOIN User ON appointments.patient_id = User.id
            WHERE appointments.doctor_id = ?
            ORDER BY appointments.appointment_date DESC, appointments.appointment_time DESC
        ");
        
        $stmt->execute([$doctorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findPrescriptions($doctorId) {
        $stmt = $this->pdo->prepare("
            SELECT prescriptions.*, medications.name as medication_name, User.first_name as patient_first_name, User.last_name as patient_last_name
            FROM prescriptions
            INNER JOIN medications ON prescriptions.medication_id = medications.id
            INNER JOIN User ON prescriptions.patient_id = User.id
            WHERE prescriptions.doctor_id = ?
            ORDER BY prescriptions.created_at DESC
        ");
        
        $stmt->execute([$doctorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
