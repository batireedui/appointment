<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Захиалгаа шалгах';

$db = getDB();
$email = trim($_GET['email'] ?? '');
$appointments = [];

if ($email !== '') {
    $stmt = $db->prepare(
        "SELECT a.*, d.name AS doctor_name, s.name AS service_name
         FROM appointments a
         JOIN doctors d ON d.id = a.doctor_id
         JOIN services s ON s.id = a.service_id
         JOIN patients p ON p.id = a.patient_id
         WHERE p.email = ?
         ORDER BY a.appointment_date DESC, a.start_time DESC"
    );
    $stmt->execute([$email]);
    $appointments = $stmt->fetchAll();
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="block container">
  <h2>Захиалгаа шалгах</h2>
  <p>Захиалгын үедээ ашигласан имэйл хаягаа оруулж, захиалгуудаа харах, цуцлах боломжтой.</p>

  <form method="get" class="form-narrow actions-row" style="align-items:flex-end;">
    <div style="flex:1;">
      <label>Имэйл</label>
      <input type="email" name="email" value="<?= e($email) ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Хайх</button>
  </form>

  <?php if ($msg = flash('cancel_success')): ?>
    <div class="alert alert-success" style="margin-top:20px;"><?= e($msg) ?></div>
  <?php endif; ?>

  <?php if ($email !== ''): ?>
    <?php if (!$appointments): ?>
      <div class="empty-state"><p>Энэ имэйлээр захиалга олдсонгүй.</p></div>
    <?php else: ?>
      <div class="table-wrap" style="margin-top:24px;">
        <table>
          <thead><tr><th>Огноо</th><th>Цаг</th><th>Эмч</th><th>Үйлчилгээ</th><th>Төлөв</th><th></th></tr></thead>
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
                  <form method="post" action="cancel-guest.php" onsubmit="return confirm('Захиалгаа цуцлахдаа итгэлтэй байна уу?');">
                    <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                    <input type="hidden" name="email" value="<?= e($email) ?>">
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
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
