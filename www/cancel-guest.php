<?php
require_once __DIR__ . '/includes/functions.php';

$id    = (int)($_POST['id'] ?? 0);
$email = trim($_POST['email'] ?? '');

$db = getDB();
$stmt = $db->prepare(
    "SELECT a.* FROM appointments a
     JOIN patients p ON p.id = a.patient_id
     WHERE a.id = ? AND p.email = ?"
);
$stmt->execute([$id, $email]);
$appt = $stmt->fetch();

if ($appt && in_array($appt['status'], ['pending', 'confirmed'])) {
    $stmt = $db->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
    $stmt->execute([$id]);
    flash('cancel_success', 'Захиалга цуцлагдлаа.');
}
redirect('find-appointments.php?email=' . urlencode($email));
