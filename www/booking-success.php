<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Захиалга баталгаажлаа';

$id = (int)($_GET['id'] ?? 0);
$db = getDB();
$stmt = $db->prepare(
    "SELECT a.*, d.name AS doctor_name, s.name AS service_name, p.name AS patient_name, p.email AS patient_email
     FROM appointments a
     JOIN doctors d ON d.id = a.doctor_id
     JOIN services s ON s.id = a.service_id
     JOIN patients p ON p.id = a.patient_id
     WHERE a.id = ?"
);
$stmt->execute([$id]);
$appt = $stmt->fetch();

require_once __DIR__ . '/includes/header.php';
?>
<section class="block container">
  <?php if (!$appt): ?>
    <div class="empty-state">
      <p>Захиалга олдсонгүй.</p>
      <a href="book.php" class="btn btn-primary">Цаг захиалах</a>
    </div>
  <?php else: ?>
    <div class="alert alert-success">Захиалга амжилттай үүслээ! Эмнэлэг баталгаажуулах хүртэл түр хүлээнэ үү.</div>
    <div class="card" style="max-width:480px;">
      <h3>Захиалгын дэлгэрэнгүй</h3>
      <p><strong>Овог нэр:</strong> <?= e($appt['patient_name']) ?></p>
      <p><strong>Үйлчилгээ:</strong> <?= e($appt['service_name']) ?></p>
      <p><strong>Эмч:</strong> <?= e($appt['doctor_name']) ?></p>
      <p><strong>Огноо:</strong> <?= e($appt['appointment_date']) ?></p>
      <p><strong>Цаг:</strong> <?= substr($appt['start_time'],0,5) ?> - <?= substr($appt['end_time'],0,5) ?></p>
      <p><strong>Төлөв:</strong> <span class="pill pill-<?= e($appt['status']) ?>"><?= statusLabel($appt['status']) ?></span></p>
    </div>
    <p class="field-hint" style="margin-top:14px;">Захиалгаа дараа шалгах, цуцлах бол
      <a href="find-appointments.php">энд</a> имэйлээрээ хайж болно (<?= e($appt['patient_email']) ?>).
    </p>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
