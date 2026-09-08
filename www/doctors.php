<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Эмч нар';
$db = getDB();
$doctors = $db->query("SELECT * FROM doctors WHERE status='active' ORDER BY name")->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>
<section class="block container">
  <h2>Эмч нар</h2>
  <p>Мэргэшлээр нь танилцаад тохирох эмчээ сонгоно уу.</p>
  <div class="grid grid-3">
    <?php foreach ($doctors as $d): ?>
      <div class="card">
        <div class="doctor-photo"></div>
        <div class="meta"><?= e($d['specialty']) ?></div>
        <h3><?= e($d['name']) ?></h3>
        <?php if ($d['bio']): ?><p><?= e($d['bio']) ?></p><?php endif; ?>
        <a href="book.php?doctor_id=<?= (int)$d['id'] ?>" class="btn btn-primary btn-sm">Цаг захиалах</a>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
