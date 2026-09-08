<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Бүртгүүлэх';
if (isPatientLoggedIn()) redirect('index.php');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $registerNo = trim($_POST['register_no'] ?? '');
    $birthDate  = $_POST['birth_date'] ?? '';
    $gender     = $_POST['gender'] ?? '';

    if ($name === '' || $phone === '' || $email === '' || $password === '') {
        $errors[] = 'Нэр, утас, имэйл, нууц үгээ бөглөнө үү.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Нууц үг дор хаяж 6 тэмдэгт байх ёстой.';
    }

    if (!$errors) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM patients WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Энэ имэйлээр аль хэдийн бүртгэл үүссэн байна.';
        }
    }

    if (!$errors) {
        $stmt = $db->prepare(
            "INSERT INTO patients (name, phone, email, password_hash, register_no, birth_date, gender)
             VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            $name, $phone, $email, password_hash($password, PASSWORD_DEFAULT),
            $registerNo ?: null, $birthDate ?: null, $gender ?: null,
        ]);
        $_SESSION['patient_id']    = $db->lastInsertId();
        $_SESSION['patient_name']  = $name;
        $_SESSION['patient_email'] = $email;
        $_SESSION['patient_phone'] = $phone;
        flash('success', 'Тавтай морил! Бүртгэл амжилттай үүслээ.');
        redirect('index.php');
    }
}
require_once __DIR__ . '/includes/header.php';
?>
<section class="block container">
  <div class="form-narrow">
    <h2>Шинэ бүртгэл</h2>
    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>
    <form method="post">
      <label>Овог нэр</label>
      <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required>

      <div class="form-row">
        <div>
          <label>Утасны дугаар</label>
          <input type="tel" name="phone" value="<?= e($_POST['phone'] ?? '') ?>" required>
        </div>
        <div>
          <label>Имэйл</label>
          <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
        </div>
      </div>

      <label>Нууц үг</label>
      <input type="password" name="password" required>
      <div class="field-hint">Дор хаяж 6 тэмдэгт</div>

      <div class="form-row">
        <div>
          <label>Төрсөн огноо</label>
          <input type="date" name="birth_date" value="<?= e($_POST['birth_date'] ?? '') ?>">
        </div>
        <div>
          <label>Хүйс</label>
          <select name="gender">
            <option value="">Сонгох</option>
            <option value="male">Эрэгтэй</option>
            <option value="female">Эмэгтэй</option>
            <option value="other">Бусад</option>
          </select>
        </div>
      </div>

      <label>Регистрийн дугаар (заавал биш)</label>
      <input type="text" name="register_no" value="<?= e($_POST['register_no'] ?? '') ?>">

      <button type="submit" class="btn btn-primary btn-block" style="margin-top:22px;">Бүртгүүлэх</button>
    </form>
    <p style="margin-top:16px;">Бүртгэлтэй юу? <a href="login.php">Нэвтрэх</a></p>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
