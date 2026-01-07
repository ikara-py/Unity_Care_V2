<?php
require_once 'BaseRepository.php';
class PrescriptionRepository extends BaseRepository {
    protected function getTableName() {
        return 'prescriptions';
    }
    public function find($id) {
        $stmt = $this->pdo->prepare("
            SELECT prescriptions.*, medications.name as medication_name, medications.description as medication_description, doctor_user.first_name as doctor_first_name, doctor_user.last_name as doctor_last_name, patient_user.first_name as patient_first_name, patient_user.last_name as patient_last_name
            FROM prescriptions
            INNER JOIN medications ON prescriptions.medication_id = medications.id
            INNER JOIN User as doctor_user ON prescriptions.doctor_id = doctor_user.id
            INNER JOIN User as patient_user ON prescriptions.patient_id = patient_user.id
            WHERE prescriptions.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function findAll() {
        $stmt = $this->pdo->query("
            SELECT prescriptions.*, medications.name as medication_name, medications.description as medication_description, doctor_user.first_name as doctor_first_name, doctor_user.last_name as doctor_last_name, patient_user.first_name as patient_first_name, patient_user.last_name as patient_last_name
            FROM prescriptions
            INNER JOIN medications ON prescriptions.medication_id = medications.id
            INNER JOIN User as doctor_user ON prescriptions.doctor_id = doctor_user.id
            INNER JOIN User as patient_user ON prescriptions.patient_id = patient_user.id
            ORDER BY prescriptions.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findByDoctor($doctorId) {
        $stmt = $this->pdo->prepare("
            SELECT prescriptions.*, medications.name as medication_name, medications.description as medication_description, doctor_user.first_name as doctor_first_name, doctor_user.last_name as doctor_last_name, patient_user.first_name as patient_first_name, patient_user.last_name as patient_last_name
            FROM prescriptions
            INNER JOIN medications ON prescriptions.medication_id = medications.id
            INNER JOIN User as doctor_user ON prescriptions.doctor_id = doctor_user.id
            INNER JOIN User as patient_user ON prescriptions.patient_id = patient_user.id
            WHERE prescriptions.doctor_id = ?
            ORDER BY prescriptions.created_at DESC
        ");
        $stmt->execute([$doctorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findByPatient($patientId) {
        $stmt = $this->pdo->prepare("
            SELECT prescriptions.*, 
                   medications.name as medication_name, 
                   medications.description as medication_description, 
                   doctor_user.first_name as doctor_first_name, 
                   doctor_user.last_name as doctor_last_name,
                   patient_user.first_name as patient_first_name, 
                   patient_user.last_name as patient_last_name
            FROM prescriptions
            INNER JOIN medications ON prescriptions.medication_id = medications.id
            INNER JOIN User as doctor_user ON prescriptions.doctor_id = doctor_user.id
            INNER JOIN User as patient_user ON prescriptions.patient_id = patient_user.id
            WHERE prescriptions.patient_id = ?
            ORDER BY prescriptions.created_at DESC
        ");
        $stmt->execute([$patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function add(Prescription $prescription) {
        $data = $prescription->toArray();
        $stmt = $this->pdo->prepare("
            INSERT INTO prescriptions (doctor_id, patient_id, medication_id, dosage_instructions) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['doctor_id'],
            $data['patient_id'],
            $data['medication_id'],
            $data['dosage_instructions']
        ]);
    }
    public function edit($id, Prescription $prescription) {
        $data = $prescription->toArray();
        $stmt = $this->pdo->prepare("
            UPDATE prescriptions SET doctor_id = ?, patient_id = ?, medication_id = ?, dosage_instructions = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['doctor_id'],
            $data['patient_id'],
            $data['medication_id'],
            $data['dosage_instructions'],
            $id
        ]);
    }
    public function getMedicationStats() {
        $stmt = $this->pdo->query("
            SELECT medications.name as medication_name, COUNT(prescriptions.id) as prescription_count, COUNT(DISTINCT prescriptions.doctor_id) as doctor_count, COUNT(DISTINCT prescriptions.patient_id) as patient_count
            FROM prescriptions
            INNER JOIN medications ON prescriptions.medication_id = medications.id
            GROUP BY medications.id, medications.name
            ORDER BY prescription_count DESC
            LIMIT 10
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}