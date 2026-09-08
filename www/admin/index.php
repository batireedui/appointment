<?php
$pageTitle = 'Хянах самбар';
require_once __DIR__ . '/includes/admin-header.php';
$db = getDB();

$today = date('Y-m-d');
$todayCount = $db->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = ? AND status IN ('pending','confirmed')");
$todayCount->execute([$today]);
$pendingCount = $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
$doctorCount  = $db->query("SELECT COUNT(*) FROM doctors WHERE status='active'")->fetchColumn();
$patientCount = $db->query("SELECT COUNT(*) FROM patients")->fetchColumn();

$stmt = $db->prepare(
    "SELECT a.*, d.name AS doctor_name, p.name AS patient_name, s.name AS service_name
     FROM appointments a
     JOIN doctors d ON d.id = a.doctor_id
     JOIN patients p ON p.id = a.patient_id
     JOIN services s ON s.id = a.service_id
     WHERE a.appointment_date = ?
     ORDER BY a.start_time"
);
$stmt->execute([$today]);
$todayAppointments = $stmt->fetchAll();
?>
<h2>Хянах самбар</h2>

<div class="stat-grid">
  <div class="stat-card"><div class="num"><?= (int)$todayCount->fetchColumn() ?></div><div class="label">Өнөөдрийн захиалга</div></div>
  <div class="stat-card"><div class="num"><?= (int)$pendingCount ?></div><div class="label">Баталгаажаагүй</div></div>
  <div class="stat-card"><div class="num"><?= (int)$doctorCount ?></div><div class="label">Идэвхтэй эмч</div></div>
  <div class="stat-card"><div class="num"><?= (int)$patientCount ?></div><div class="label">Бүртгэлтэй өвчтөн</div></div>
</div>

<h3>Өнөөдрийн цагийн хуваарь</h3>
<?php if (!$todayAppointments): ?>
  <p class="muted">Өнөөдөр захиалга алга байна.</p>
<?php else: ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Цаг</th><th>Өвчтөн</th><th>Эмч</th><th>Үйлчилгээ</th><th>Төлөв</th></tr></thead>
      <tbody>
      <?php foreach ($todayAppointments as $a): ?>
        <tr>
          <td><?= substr($a['start_time'],0,5) ?></td>
          <td><?= e($a['patient_name']) ?></td>
          <td><?= e($a['doctor_name']) ?></td>
          <td><?= e($a['service_name']) ?></td>
          <td><span class="pill pill-<?= e($a['status']) ?>"><?= statusLabel($a['status']) ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
