<?php
$pageTitle = 'Эмч нар';
require_once __DIR__ . '/includes/admin-header.php';
$db = getDB();
$errors = [];
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $specialty = trim($_POST['specialty'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $status = $_POST['status'] ?? 'active';

        if ($name === '' || $specialty === '') {
            $errors[] = 'Нэр болон мэргэшлийг бөглөнө үү.';
        }

        if (!$errors) {
            if ($id) {
                $stmt = $db->prepare("UPDATE doctors SET name=?, phone=?, email=?, specialty=?, bio=?, status=? WHERE id=?");
                $stmt->execute([$name, $phone, $email, $specialty, $bio, $status, $id]);
            } else {
                $stmt = $db->prepare("INSERT INTO doctors (name, phone, email, specialty, bio, status) VALUES (?,?,?,?,?,?)");
                $stmt->execute([$name, $phone, $email, $specialty, $bio, $status]);
            }
            redirect('doctors.php');
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("UPDATE doctors SET status='inactive' WHERE id=?");
        $stmt->execute([$id]);
        redirect('doctors.php');
    }
}

if (!empty($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$doctors = $db->query("SELECT * FROM doctors ORDER BY status DESC, name")->fetchAll();
?>
<div class="block-head">
  <h2>Эмч нар</h2>
</div>

<div class="grid grid-2" style="align-items:start;">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Нэр</th><th>Мэргэшил</th><th>Утас</th><th>Төлөв</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($doctors as $d): ?>
        <tr>
          <td><?= e($d['name']) ?></td>
          <td><?= e($d['specialty']) ?></td>
          <td><?= e($d['phone']) ?></td>
          <td><span class="pill pill-<?= $d['status']==='active'?'confirmed':'cancelled' ?>"><?= $d['status']==='active'?'Идэвхтэй':'Идэвхгүй' ?></span></td>
          <td class="actions-row">
            <a href="doctors.php?edit=<?= (int)$d['id'] ?>" class="btn btn-outline btn-sm">Засах</a>
            <?php if ($d['status']==='active'): ?>
              <form method="post" onsubmit="return confirm('Идэвхгүй болгох уу?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
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
    <h3><?= $editing ? 'Эмч засах' : 'Шинэ эмч нэмэх' ?></h3>
    <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
    <form method="post">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">
      <label>Овог нэр</label>
      <input type="text" name="name" value="<?= e($editing['name'] ?? '') ?>" required>
      <div class="form-row">
        <div><label>Утас</label><input type="tel" name="phone" value="<?= e($editing['phone'] ?? '') ?>"></div>
        <div><label>Имэйл</label><input type="email" name="email" value="<?= e($editing['email'] ?? '') ?>"></div>
      </div>
      <label>Мэргэшил</label>
      <input type="text" name="specialty" value="<?= e($editing['specialty'] ?? '') ?>" required>
      <label>Товч танилцуулга</label>
      <textarea name="bio"><?= e($editing['bio'] ?? '') ?></textarea>
      <label>Төлөв</label>
      <select name="status">
        <option value="active" <?= ($editing['status'] ?? 'active')==='active'?'selected':'' ?>>Идэвхтэй</option>
        <option value="inactive" <?= ($editing['status'] ?? '')==='inactive'?'selected':'' ?>>Идэвхгүй</option>
      </select>
      <button type="submit" class="btn btn-primary btn-block" style="margin-top:18px;"><?= $editing ? 'Хадгалах' : 'Нэмэх' ?></button>
      <?php if ($editing): ?><a href="doctors.php" class="btn btn-outline btn-block" style="margin-top:8px;">Цуцлах</a><?php endif; ?>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
