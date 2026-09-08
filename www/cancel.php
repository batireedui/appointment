<?php
require_once __DIR__ . '/includes/functions.php';
requirePatientLogin();

$id = (int)($_POST['id'] ?? 0);
$db = getDB();
$stmt = $db->prepare("SELECT * FROM appointments WHERE id = ? AND patient_id = ?");
$stmt->execute([$id, $_SESSION['patient_id']]);
$appt = $stmt->fetch();

if ($appt && in_array($appt['status'], ['pending', 'confirmed'])) {
    $stmt = $db->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
    $stmt->execute([$id]);
    flash('cancel_success', 'Захиалга цуцлагдлаа.');
}
redirect('my-appointments.php');
