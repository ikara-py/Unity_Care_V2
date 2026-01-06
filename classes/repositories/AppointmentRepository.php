<?php
require_once 'BaseRepository.php';
class AppointmentRepository extends BaseRepository {
    protected function getTableName() {
        return 'appointments';
    }
    public function find($id) {
        $stmt = $this->pdo->prepare("
            SELECT appointments.*, doctor_user.first_name as doctor_first_name, doctor_user.last_name as doctor_last_name, patient_user.first_name as patient_first_name, patient_user.last_name as patient_last_name
            FROM appointments
            INNER JOIN User as doctor_user ON appointments.doctor_id = doctor_user.id
            INNER JOIN User as patient_user ON appointments.patient_id = patient_user.id
            WHERE appointments.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function findAll() {
        $stmt = $this->pdo->query("
            SELECT appointments.*, doctor_user.first_name as doctor_first_name, doctor_user.last_name as doctor_last_name, patient_user.first_name as patient_first_name, patient_user.last_name as patient_last_name
            FROM appointments
            INNER JOIN User as doctor_user ON appointments.doctor_id = doctor_user.id
            INNER JOIN User as patient_user ON appointments.patient_id = patient_user.id
            ORDER BY appointments.appointment_date DESC, appointments.appointment_time DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findByDoctor($doctorId) {
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
    public function findByPatient($patientId) {
        $stmt = $this->pdo->prepare("
            SELECT appointments.*, User.first_name as doctor_first_name, User.last_name as doctor_last_name
            FROM appointments
            INNER JOIN User ON appointments.doctor_id = User.id
            WHERE appointments.patient_id = ?
            ORDER BY appointments.appointment_date DESC, appointments.appointment_time DESC
        ");
        $stmt->execute([$patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function add(Appointment $appointment) {
        $data = $appointment->toArray();
        $stmt = $this->pdo->prepare("
            INSERT INTO appointments (appointment_date, appointment_time, doctor_id, patient_id, reason, status) 
            VALUES (?, ?, ?, ?, ?, 'scheduled')
        ");
        return $stmt->execute([
            $data['appointment_date'],
            $data['appointment_time'],
            $data['doctor_id'],
            $data['patient_id'],
            $data['reason']
        ]);
    }
    public function edit($id, Appointment $appointment) {
        $data = $appointment->toArray();
        $stmt = $this->pdo->prepare("
            UPDATE appointments 
            SET appointment_date = ?, appointment_time = ?, doctor_id = ?, patient_id = ?, reason = ?, status = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['appointment_date'],
            $data['appointment_time'],
            $data['doctor_id'],
            $data['patient_id'],
            $data['reason'],
            $data['status'],
            $id
        ]);
    }
    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare("
            UPDATE appointments 
            SET status = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$status, $id]);
    }
    public function getStats() {
        $stmt = $this->pdo->query("
            SELECT appointments.status, COUNT(appointments.id) as count, COUNT(DISTINCT appointments.doctor_id) as doctor_count, COUNT(DISTINCT appointments.patient_id) as patient_count
            FROM appointments
            GROUP BY appointments.status
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getMonthlyStats() {
        $stmt = $this->pdo->query("
            SELECT DATE_FORMAT(appointments.appointment_date, '%Y-%m') as month, COUNT(appointments.id) as total, SUM(CASE WHEN appointments.status = 'scheduled' THEN 1 ELSE 0 END) as scheduled, SUM(CASE WHEN appointments.status = 'done' THEN 1 ELSE 0 END) as done, SUM(CASE WHEN appointments.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            FROM appointments
            WHERE appointments.appointment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(appointments.appointment_date, '%Y-%m')
            ORDER BY month DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}