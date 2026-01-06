<?php
require_once 'BaseRepository.php';
class PatientRepository extends BaseRepository {
    protected function getTableName() {
        return 'patients';
    }
    public function find($id) {
        $stmt = $this->pdo->prepare(" SELECT User.*, patients.date_of_birth, patients.phone FROM patients INNER JOIN User ON patients.id = User.id WHERE patients.id = ? ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function findAll() {
        $stmt = $this->pdo->query(" SELECT User.*, patients.date_of_birth, patients.phone FROM patients INNER JOIN User ON patients.id = User.id ORDER BY User.last_name, User.first_name ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function add(Patient $patient) {
        $data = $patient->toArray();
        $stmt = $this->pdo->prepare(" INSERT INTO patients (id, date_of_birth, phone) VALUES (?, ?, ?) ");
        return $stmt->execute([
            $data['id'],
            $data['date_of_birth'],
            $data['phone']
        ]);
    }
    public function edit($id, Patient $patient) {
        $data = $patient->toArray();
        $stmt = $this->pdo->prepare("UPDATE patients SET date_of_birth = ?, phone = ? WHERE id = ? ");
        return $stmt->execute([
            $data['date_of_birth'],
            $data['phone'],
            $id
        ]);
    }
    public function findAppointments($patientId) {
        $stmt = $this->pdo->prepare(" SELECT appointments.*, User.first_name as doctor_first_name, User.last_name as doctor_last_name
            FROM appointments
            INNER JOIN User ON appointments.doctor_id = User.id
            WHERE appointments.patient_id = ?
            ORDER BY appointments.appointment_date DESC, appointments.appointment_time DESC
        ");
        $stmt->execute([$patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findPrescriptions($patientId) {
        $stmt = $this->pdo->prepare(" SELECT prescriptions.*, medications.name as medication_name, medications.description as medication_description, User.first_name as doctor_first_name, User.last_name as doctor_last_name
            FROM prescriptions
            INNER JOIN medications ON prescriptions.medication_id = medications.id
            INNER JOIN User ON prescriptions.doctor_id = User.id
            WHERE prescriptions.patient_id = ?
            ORDER BY prescriptions.created_at DESC
        ");
        $stmt->execute([$patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}