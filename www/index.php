<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Нүүр';
$db = getDB();
$doctors = $db->query("SELECT * FROM doctors WHERE status='active' ORDER BY name LIMIT 3")->fetchAll();
$services = $db->query("SELECT * FROM services WHERE status='active' ORDER BY name LIMIT 4")->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container hero-grid">
    <div>
      <span class="kicker">Онлайн цаг захиалга</span>
      <h1>Эмчид үзүүлэх цагаа гурван товшилтоор захиал.</h1>
      <p class="lede">Дараалалд зогсох шаардлагагүй. Үйлчилгээгээ сонгоод, чөлөөтэй эмчээ, тохирох цагаа олж, шууд баталгаажуулна.</p>
      <div class="hero-actions">
        <a href="book.php" class="btn btn-accent">Цаг захиалах</a>
        <a href="doctors.php" class="btn btn-outline">Эмч нартай танилцах</a>
      </div>
    </div>
    <div class="hero-card">
      <div class="step-row">
        <div class="step-num">1</div>
        <div class="step-copy"><strong>Үйлчилгээгээ сонго</strong><span>Ямар үзлэг хэрэгтэй байгаагаа сонгоно</span></div>
      </div>
      <div class="step-row">
        <div class="step-num">2</div>
        <div class="step-copy"><strong>Эмч, огноо сонго</strong><span>Чөлөөтэй цагуудаас өөрт тохирохыг сонгоно</span></div>
      </div>
      <div class="step-row">
        <div class="step-num">3</div>
        <div class="step-copy"><strong>Баталгаажуулна</strong><span>Захиалга шууд баталгаажиж, түүхэндээ хадгалагдана</span></div>
      </div>
    </div>
  </div>
</section>

<section class="block container">
  <div class="block-head">
    <h2>Түгээмэл үйлчилгээ</h2>
    <a href="services.php" class="muted">Бүх үйлчилгээг харах →</a>
  </div>
  <div class="grid grid-3">
    <?php foreach ($services as $s): ?>
      <div class="card">
        <div class="meta"><?= (int)$s['duration'] ?> минут</div>
        <h3><?= e($s['name']) ?></h3>
        <p><?= e($s['description']) ?></p>
        <div class="price"><?= formatMoney((float)$s['price']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="block container">
  <div class="block-head">
    <h2>Манай эмч нар</h2>
    <a href="doctors.php" class="muted">Бүх эмчийг харах →</a>
  </div>
  <div class="grid grid-3">
    <?php foreach ($doctors as $d): ?>
      <div class="card">
        <div class="doctor-photo"></div>
        <div class="meta"><?= e($d['specialty']) ?></div>
        <h3><?= e($d['name']) ?></h3>
        <a href="book.php?doctor_id=<?= (int)$d['id'] ?>" class="btn btn-outline btn-sm">Цаг захиалах</a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
