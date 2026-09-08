<?php
$pageTitle = 'Үйлчилгээ';
require_once __DIR__ . '/includes/admin-header.php';
$db = getDB();
$errors = [];
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $duration = (int)($_POST['duration'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        if ($name === '' || $duration <= 0) {
            $errors[] = 'Нэр болон хугацааг (минут) зөв бөглөнө үү.';
        }

        if (!$errors) {
            if ($id) {
                $stmt = $db->prepare("UPDATE services SET name=?, description=?, duration=?, price=?, status=? WHERE id=?");
                $stmt->execute([$name, $description, $duration, $price, $status, $id]);
            } else {
                $stmt = $db->prepare("INSERT INTO services (name, description, duration, price, status) VALUES (?,?,?,?,?)");
                $stmt->execute([$name, $description, $duration, $price, $status]);
            }
            redirect('services.php');
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("UPDATE services SET status='inactive' WHERE id=?");
        $stmt->execute([$id]);
        redirect('services.php');
    }
}

if (!empty($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$services = $db->query("SELECT * FROM services ORDER BY status DESC, name")->fetchAll();
?>
<div class="block-head"><h2>Үйлчилгээ</h2></div>

<div class="grid grid-2" style="align-items:start;">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Нэр</th><th>Хугацаа</th><th>Үнэ</th><th>Төлөв</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($services as $s): ?>
        <tr>
          <td><?= e($s['name']) ?></td>
          <td><?= (int)$s['duration'] ?> мин</td>
          <td><?= formatMoney((float)$s['price']) ?></td>
          <td><span class="pill pill-<?= $s['status']==='active'?'confirmed':'cancelled' ?>"><?= $s['status']==='active'?'Идэвхтэй':'Идэвхгүй' ?></span></td>
          <td class="actions-row">
            <a href="services.php?edit=<?= (int)$s['id'] ?>" class="btn btn-outline btn-sm">Засах</a>
            <?php if ($s['status']==='active'): ?>
              <form method="post" onsubmit="return confirm('Идэвхгүй болгох уу?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                <button class="btn btn-danger btn-sm">Идэвхгүй болгох</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h3><?= $editing ? 'Үйлчилгээ засах' : 'Шинэ үйлчилгээ нэмэх' ?></h3>
    <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
    <form method="post">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">
      <label>Нэр</label>
      <input type="text" name="name" value="<?= e($editing['name'] ?? '') ?>" required>
      <label>Тайлбар</label>
      <textarea name="description"><?= e($editing['description'] ?? '') ?></textarea>
      <div class="form-row">
        <div><label>Хугацаа (минут)</label><input type="number" name="duration" min="5" step="5" value="<?= e((string)($editing['duration'] ?? 20)) ?>" required></div>
        <div><label>Үнэ (₮)</label><input type="number" name="price" min="0" step="1000" value="<?= e((string)($editing['price'] ?? 0)) ?>" required></div>
      </div>
      <label>Төлөв</label>
      <select name="status">
        <option value="active" <?= ($editing['status'] ?? 'active')==='active'?'selected':'' ?>>Идэвхтэй</option>
        <option value="inactive" <?= ($editing['status'] ?? '')==='inactive'?'selected':'' ?>>Идэвхгүй</option>
      </select>
      <button type="submit" class="btn btn-primary btn-block" style="margin-top:18px;"><?= $editing ? 'Хадгалах' : 'Нэмэх' ?></button>
      <?php if ($editing): ?><a href="services.php" class="btn btn-outline btn-block" style="margin-top:8px;">Цуцлах</a><?php endif; ?>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
