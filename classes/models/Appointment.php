<?php
class Appointment {
    private $id;
    private $appointment_date;
    private $appointment_time;
    private $doctor_id;
    private $patient_id;
    private $reason;
    private $status;
    private $created_at;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->appointment_date = $data['appointment_date'] ?? '';
        $this->appointment_time = $data['appointment_time'] ?? '';
        $this->doctor_id = $data['doctor_id'] ?? null;
        $this->patient_id = $data['patient_id'] ?? null;
        $this->reason = $data['reason'] ?? '';
        $this->status = $data['status'] ?? 'scheduled';
        $this->created_at = $data['created_at'] ?? null;
    }

    public function getId() { return $this->id; }
    public function getAppointmentDate() { return $this->appointment_date; }
    public function getAppointmentTime() { return $this->appointment_time; }
    public function getDoctorId() { return $this->doctor_id; }
    public function getPatientId() { return $this->patient_id; }
    public function getReason() { return $this->reason; }
    public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->created_at; }

    public function setId($id) { $this->id = $id; }
    public function setAppointmentDate($appointment_date) { $this->appointment_date = $appointment_date; }
    public function setAppointmentTime($appointment_time) { $this->appointment_time = $appointment_time; }
    public function setDoctorId($doctor_id) { $this->doctor_id = $doctor_id; }
    public function setPatientId($patient_id) { $this->patient_id = $patient_id; }
    public function setReason($reason) { $this->reason = $reason; }
    public function setStatus($status) { $this->status = $status; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
}