<?php
require_once 'includes/header.php';
require_once 'classes/repositories/AppointmentRepository.php';
require_once 'classes/repositories/PrescriptionRepository.php';

$appointmentRepo = new AppointmentRepository();
$prescriptionRepo = new PrescriptionRepository();
$stats = [];

if ($user['role'] === 'admin') {
    $stats = [
        'total_appointments' => count($appointmentRepo->findAll()),
        'scheduled_appointments' => count(array_filter($appointmentRepo->findAll(), fn($a) => $a['status'] === 'scheduled')),
        'monthly_appointments' => $appointmentRepo->getMonthlyStats(),
        'appointment_stats' => $appointmentRepo->getStats(),
        'medication_stats' => $prescriptionRepo->getMedicationStats()
    ];
} elseif ($user['role'] === 'doctor') {
    $stats = [
        'my_appointments' => $appointmentRepo->findByDoctor($user['id']),
        'my_prescriptions' => $prescriptionRepo->findByDoctor($user['id'])
    ];
} elseif ($user['role'] === 'patient') {
    $stats = [
        'my_appointments' => $appointmentRepo->findByPatient($user['id']),
        'my_prescriptions' => $prescriptionRepo->findByPatient($user['id'])
    ];
}
?>
        <h2>Dashboard</h2>
        
        <?php if ($user['role'] === 'admin'): ?>
            <div class="dashboard-grid">
                <div class="stat-card">
                    <h3>Total Appointments</h3>
                    <div class="number"><?= $stats['total_appointments'] ?></div>
                </div>
                <div class="stat-card">
                    <h3>Scheduled</h3>
                    <div class="number"><?= $stats['scheduled_appointments'] ?></div>
                </div>
            </div>
            
            <h3 class="mb-2">Monthly Statistics</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total</th>
                            <th>Scheduled</th>
                            <th>Done</th>
                            <th>Cancelled</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['monthly_appointments'] as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['month']) ?></td>
                                <td><?= htmlspecialchars($row['total']) ?></td>
                                <td><?= htmlspecialchars($row['scheduled']) ?></td>
                                <td><?= htmlspecialchars($row['done']) ?></td>
                                <td><?= htmlspecialchars($row['cancelled']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <h3 class="mb-2">Top Medications</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Medication</th>
                            <th>Prescriptions</th>
                            <th>Doctors</th>
                            <th>Patients</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['medication_stats'] as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['medication_name']) ?></td>
                                <td><?= htmlspecialchars($row['prescription_count']) ?></td>
                                <td><?= htmlspecialchars($row['doctor_count']) ?></td>
                                <td><?= htmlspecialchars($row['patient_count']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
        <?php elseif ($user['role'] === 'doctor'): ?>
            <h3>Your Upcoming Appointments</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['my_appointments'] as $appointment): ?>
                            <tr>
                                <td><?= formatDate($appointment['appointment_date']) ?></td>
                                <td><?= formatTime($appointment['appointment_time']) ?></td>
                                <td><?= htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']) ?></td>
                                <td><?= htmlspecialchars($appointment['reason']) ?></td>
                                <td><?= htmlspecialchars($appointment['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
        <?php elseif ($user['role'] === 'patient'): ?>
            <h3>My Appointments</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Doctor</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['my_appointments'] as $appointment): ?>
                            <tr>
                                <td><?= formatDate($appointment['appointment_date']) ?></td>
                                <td><?= formatTime($appointment['appointment_time']) ?></td>
                                <td><?= htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']) ?></td>
                                <td><?= htmlspecialchars($appointment['reason']) ?></td>
                                <td><?= htmlspecialchars($appointment['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
<?php
require_once 'includes/footer.php';
?>