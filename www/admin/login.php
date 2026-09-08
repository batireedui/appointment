<?php
require_once __DIR__ . '/../includes/functions.php';
if (isAdminLoggedIn()) redirect('index.php');

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $error = 'Нэвтрэх нэр эсвэл нууц үг буруу байна.';
    } elseif ($user['status'] !== 'active') {
        $error = 'Таны эрх идэвхгүй байна.';
    } else {
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_name'] = $user['full_name'];
        $_SESSION['admin_role'] = $user['role'];
        redirect('index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ажилтны нэвтрэх</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background:var(--pine-950); min-height:100vh; display:flex; align-items:center; justify-content:center;">
  <div class="card" style="width:360px; background:#fff;">
    <h2 style="text-align:center;">Ажилтны нэвтрэх</h2>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <label>Нэвтрэх нэр</label>
      <input type="text" name="username" required autofocus>
      <label>Нууц үг</label>
      <input type="password" name="password" required>
      <button type="submit" class="btn btn-primary btn-block" style="margin-top:20px;">Нэвтрэх</button>
    </form>
    <p style="text-align:center; margin-top:16px;"><a href="../index.php">← Сайт руу буцах</a></p>
  </div>
</body>
</html>
