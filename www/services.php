<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Үйлчилгээ';
$db = getDB();
$services = $db->query("SELECT * FROM services WHERE status='active' ORDER BY name")->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>
<section class="block container">
  <h2>Үйлчилгээнүүд</h2>
  <p>Хэрэгтэй үзлэгээ сонгоод шууд цаг захиалах боломжтой.</p>
  <div class="grid grid-3">
    <?php foreach ($services as $s): ?>
      <div class="card">
        <div class="meta"><?= (int)$s['duration'] ?> минут</div>
        <h3><?= e($s['name']) ?></h3>
        <p><?= e($s['description']) ?></p>
        <div class="price"><?= formatMoney((float)$s['price']) ?></div>
        <a href="book.php?service_id=<?= (int)$s['id'] ?>" class="btn btn-primary btn-sm" style="margin-top:12px;">Цаг захиалах</a>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
