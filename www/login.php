<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Нэвтрэх';
if (isPatientLoggedIn()) redirect('index.php');

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM patients WHERE email = ?");
    $stmt->execute([$email]);
    $patient = $stmt->fetch();

    if (!$patient || !password_verify($password, $patient['password_hash'])) {
        $error = 'Имэйл эсвэл нууц үг буруу байна.';
    } elseif ($patient['status'] !== 'active') {
        $error = 'Таны бүртгэл идэвхгүй байна.';
    } else {
        $_SESSION['patient_id']    = $patient['id'];
        $_SESSION['patient_name']  = $patient['name'];
        $_SESSION['patient_email'] = $patient['email'];
        $_SESSION['patient_phone'] = $patient['phone'];
        redirect('index.php');
    }
}
require_once __DIR__ . '/includes/header.php';
?>
<section class="block container">
  <div class="form-narrow">
    <h2>Нэвтрэх</h2>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <label>Имэйл</label>
      <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required autofocus>
      <label>Нууц үг</label>
      <input type="password" name="password" required>
      <button type="submit" class="btn btn-primary btn-block" style="margin-top:22px;">Нэвтрэх</button>
    </form>
    <p style="margin-top:16px;">Бүртгэлгүй юу? <a href="register.php">Бүртгүүлэх</a></p>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
