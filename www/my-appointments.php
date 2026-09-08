<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Миний захиалга';
requirePatientLogin();

$db = getDB();
$stmt = $db->prepare(
    "SELECT a.*, d.name AS doctor_name, s.name AS service_name
     FROM appointments a
     JOIN doctors d ON d.id = a.doctor_id
     JOIN services s ON s.id = a.service_id
     WHERE a.patient_id = ?
     ORDER BY a.appointment_date DESC, a.start_time DESC"
);
$stmt->execute([$_SESSION['patient_id']]);
$appointments = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<section class="block container">
  <h2>Миний захиалга</h2>

  <?php if (!empty($_GET['booked'])): ?>
    <div class="alert alert-success">Захиалга амжилттай үүслээ! Эмнэлэг баталгаажуулах хүртэл түр хүлээнэ үү.</div>
  <?php endif; ?>
  <?php if ($msg = flash('cancel_success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
  <?php endif; ?>

  <?php if (!$appointments): ?>
    <div class="empty-state">
      <p>Танд одоогоор захиалга алга байна.</p>
      <a href="book.php" class="btn btn-primary">Цаг захиалах</a>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Огноо</th><th>Цаг</th><th>Эмч</th><th>Үйлчилгээ</th><th>Төлөв</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($appointments as $a): ?>
          <tr>
            <td><?= e($a['appointment_date']) ?></td>
            <td><?= substr($a['start_time'],0,5) ?> - <?= substr($a['end_time'],0,5) ?></td>
            <td><?= e($a['doctor_name']) ?></td>
            <td><?= e($a['service_name']) ?></td>
            <td><span class="pill pill-<?= e($a['status']) ?>"><?= statusLabel($a['status']) ?></span></td>
            <td class="text-right">
              <?php if (in_array($a['status'], ['pending','confirmed']) && $a['appointment_date'] >= date('Y-m-d')): ?>
                <form method="post" action="cancel.php" onsubmit="return confirm('Захиалгаа цуцлахдаа итгэлтэй байна уу?');">
                  <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm">Цуцлах</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
