<?php
class Prescription {
    private $id;
    private $doctor_id;
    private $patient_id;
    private $medication_id;
    private $dosage_instructions;
    private $created_at;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->doctor_id = $data['doctor_id'] ?? null;
        $this->patient_id = $data['patient_id'] ?? null;
        $this->medication_id = $data['medication_id'] ?? null;
        $this->dosage_instructions = $data['dosage_instructions'] ?? '';
        $this->created_at = $data['created_at'] ?? null;
    }

    public function getId() { return $this->id; }
    public function getDoctorId() { return $this->doctor_id; }
    public function getPatientId() { return $this->patient_id; }
    public function getMedicationId() { return $this->medication_id; }
    public function getDosageInstructions() { return $this->dosage_instructions; }
    public function getCreatedAt() { return $this->created_at; }

    public function setId($id) { $this->id = $id; }
    public function setDoctorId($doctor_id) { $this->doctor_id = $doctor_id; }
    public function setPatientId($patient_id) { $this->patient_id = $patient_id; }
    public function setMedicationId($medication_id) { $this->medication_id = $medication_id; }
    public function setDosageInstructions($dosage_instructions) { $this->dosage_instructions = $dosage_instructions; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
}