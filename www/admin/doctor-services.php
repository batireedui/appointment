<?php
$pageTitle = 'Эмч ↔ Үйлчилгээ';
require_once __DIR__ . '/includes/admin-header.php';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $doctorId  = (int)$_POST['doctor_id'];
        $serviceId = (int)$_POST['service_id'];
        $price     = $_POST['price'] !== '' ? (float)$_POST['price'] : null;
        $stmt = $db->prepare(
            "INSERT INTO doctor_services (doctor_id, service_id, price, status)
             VALUES (?,?,?,'active')
             ON DUPLICATE KEY UPDATE price = VALUES(price), status='active'"
        );
        $stmt->execute([$doctorId, $serviceId, $price]);
    } elseif ($action === 'remove') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("DELETE FROM doctor_services WHERE id = ?");
        $stmt->execute([$id]);
    }
    redirect('doctor-services.php');
}

$doctors  = $db->query("SELECT * FROM doctors WHERE status='active' ORDER BY name")->fetchAll();
$services = $db->query("SELECT * FROM services WHERE status='active' ORDER BY name")->fetchAll();
$links = $db->query(
    "SELECT ds.*, d.name AS doctor_name, s.name AS service_name, s.price AS base_price
     FROM doctor_services ds
     JOIN doctors d ON d.id = ds.doctor_id
     JOIN services s ON s.id = ds.service_id
     WHERE ds.status='active'
     ORDER BY d.name, s.name"
)->fetchAll();
?>
<div class="block-head"><h2>Эмч ↔ Үйлчилгээ холбох</h2></div>
<p>Эмч тус бүр ямар үйлчилгээ үзүүлдгийг тохируулна. Онцгой үнэ оруулаагүй бол үйлчилгээний үндсэн үнийг ашиглана.</p>

<div class="grid grid-2" style="align-items:start;">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Эмч</th><th>Үйлчилгээ</th><th>Үнэ</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($links as $l): ?>
        <tr>
          <td><?= e($l['doctor_name']) ?></td>
          <td><?= e($l['service_name']) ?></td>
          <td><?= formatMoney((float)($l['price'] ?? $l['base_price'])) ?><?= $l['price'] === null ? ' <span class="muted">(үндсэн)</span>' : '' ?></td>
          <td>
            <form method="post" onsubmit="return confirm('Холбоог хасах уу?');">
              <input type="hidden" name="action" value="remove">
              <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
              <button class="btn btn-danger btn-sm">Хасах</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h3>Шинэ холбоос нэмэх</h3>
    <form method="post">
      <input type="hidden" name="action" value="add">
      <label>Эмч</label>
      <select name="doctor_id" required>
        <?php foreach ($doctors as $d): ?><option value="<?= (int)$d['id'] ?>"><?= e($d['name']) ?></option><?php endforeach; ?>
      </select>
      <label>Үйлчилгээ</label>
      <select name="service_id" required>
        <?php foreach ($services as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?> (<?= formatMoney((float)$s['price']) ?>)</option><?php endforeach; ?>
      </select>
      <label>Онцгой үнэ (заавал биш)</label>
      <input type="number" name="price" min="0" step="1000" placeholder="Хоосон бол үндсэн үнэ хэрэглэнэ">
      <button type="submit" class="btn btn-primary btn-block" style="margin-top:18px;">Холбох</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
