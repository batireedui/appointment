<?php
$pageTitle = 'Захиалгууд';
require_once __DIR__ . '/includes/admin-header.php';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    if (in_array($status, ['pending','confirmed','completed','cancelled','no_show'], true)) {
        $stmt = $db->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
    redirect('appointments.php' . (!empty($_GET['date']) ? '?date=' . urlencode($_GET['date']) : ''));
}

$dateFilter = $_GET['date'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT a.*, d.name AS doctor_name, p.name AS patient_name, p.phone AS patient_phone, s.name AS service_name
        FROM appointments a
        JOIN doctors d ON d.id = a.doctor_id
        JOIN patients p ON p.id = a.patient_id
        JOIN services s ON s.id = a.service_id
        WHERE 1=1";
$params = [];
if ($dateFilter) { $sql .= " AND a.appointment_date = ?"; $params[] = $dateFilter; }
if ($statusFilter) { $sql .= " AND a.status = ?"; $params[] = $statusFilter; }
$sql .= " ORDER BY a.appointment_date DESC, a.start_time DESC LIMIT 200";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll();
?>
<div class="block-head">
  <h2>Захиалгууд</h2>
</div>

<form method="get" class="actions-row" style="margin-bottom:20px; align-items:flex-end;">
  <div>
    <label>Огноо</label>
    <input type="date" name="date" value="<?= e($dateFilter) ?>">
  </div>
  <div>
    <label>Төлөв</label>
    <select name="status">
      <option value="">Бүгд</option>
      <?php foreach (STATUS_LABELS_MN as $key => $label): ?>
        <option value="<?= $key ?>" <?= $statusFilter===$key?'selected':'' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <button type="submit" class="btn btn-outline btn-sm">Шүүх</button>
  <a href="appointments.php" class="btn btn-outline btn-sm">Цэвэрлэх</a>
</form>

<div class="table-wrap">
  <table>
    <thead>
      <tr><th>Огноо</th><th>Цаг</th><th>Өвчтөн</th><th>Утас</th><th>Эмч</th><th>Үйлчилгээ</th><th>Төлөв</th><th></th></tr>
    </thead>
    <tbody>
    <?php if (!$appointments): ?>
      <tr><td colspan="8" class="muted" style="text-align:center;">Захиалга олдсонгүй.</td></tr>
    <?php endif; ?>
    <?php foreach ($appointments as $a): ?>
      <tr>
        <td><?= e($a['appointment_date']) ?></td>
        <td><?= substr($a['start_time'],0,5) ?></td>
        <td><?= e($a['patient_name']) ?></td>
        <td><?= e($a['patient_phone']) ?></td>
        <td><?= e($a['doctor_name']) ?></td>
        <td><?= e($a['service_name']) ?></td>
        <td><span class="pill pill-<?= e($a['status']) ?>"><?= statusLabel($a['status']) ?></span></td>
        <td>
          <form method="post" class="actions-row">
            <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
            <select name="status" onchange="this.form.submit()">
              <?php foreach (STATUS_LABELS_MN as $key => $label): ?>
                <option value="<?= $key ?>" <?= $a['status']===$key?'selected':'' ?>><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
