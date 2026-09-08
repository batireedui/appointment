<?php
$pageTitle = 'Өвчтөнүүд';
require_once __DIR__ . '/includes/admin-header.php';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $status = $_POST['status'] === 'active' ? 'active' : 'inactive';
    $stmt = $db->prepare("UPDATE patients SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    redirect('patients.php');
}

$search = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM patients WHERE 1=1";
$params = [];
if ($search !== '') {
    $sql .= " AND (name LIKE ? OR phone LIKE ? OR email LIKE ?)";
    $like = "%$search%";
    $params = [$like, $like, $like];
}
$sql .= " ORDER BY created_at DESC LIMIT 200";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$patients = $stmt->fetchAll();
?>
<div class="block-head"><h2>Өвчтөнүүд</h2></div>

<form method="get" style="margin-bottom:18px; max-width:340px;">
  <input type="text" name="q" placeholder="Нэр, утас, имэйлээр хайх" value="<?= e($search) ?>">
</form>

<div class="table-wrap">
  <table>
    <thead><tr><th>Нэр</th><th>Утас</th><th>Имэйл</th><th>Бүртгэсэн</th><th>Төлөв</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($patients as $p): ?>
      <tr>
        <td><?= e($p['name']) ?></td>
        <td><?= e($p['phone']) ?></td>
        <td><?= e($p['email']) ?></td>
        <td><?= e(substr($p['created_at'],0,10)) ?></td>
        <td><span class="pill pill-<?= $p['status']==='active'?'confirmed':'cancelled' ?>"><?= $p['status']==='active'?'Идэвхтэй':'Идэвхгүй' ?></span></td>
        <td>
          <form method="post">
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <input type="hidden" name="status" value="<?= $p['status']==='active'?'inactive':'active' ?>">
            <button class="btn btn-outline btn-sm"><?= $p['status']==='active'?'Идэвхгүй болгох':'Идэвхжүүлэх' ?></button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$patients): ?><tr><td colspan="6" class="muted" style="text-align:center;">Өвчтөн олдсонгүй.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
