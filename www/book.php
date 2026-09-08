<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Цаг захиалах';

$db = getDB();
$services = $db->query("SELECT * FROM services WHERE status='active' ORDER BY name")->fetchAll();
$doctors  = $db->query("SELECT * FROM doctors WHERE status='active' ORDER BY name")->fetchAll();
$links    = $db->query("SELECT doctor_id, service_id, price FROM doctor_services WHERE status='active'")->fetchAll();

$serviceToDoctors = [];
$doctorToServices = [];
foreach ($links as $l) {
    $serviceToDoctors[$l['service_id']][] = (int)$l['doctor_id'];
    $doctorToServices[$l['doctor_id']][]  = (int)$l['service_id'];
}

$preselectService = (int)($_GET['service_id'] ?? 0);
$preselectDoctor  = (int)($_GET['doctor_id'] ?? 0);

require_once __DIR__ . '/includes/header.php';
?>
<section class="block container">
  <h2>Цаг захиалах</h2>

  <div class="flow-steps" id="flowSteps">
    <div class="flow-step active" data-step="1"><span class="dot">1</span> Үйлчилгээ</div>
    <div class="flow-step" data-step="2"><span class="dot">2</span> Эмч</div>
    <div class="flow-step" data-step="3"><span class="dot">3</span> Огноо, цаг</div>
    <div class="flow-step" data-step="4"><span class="dot">4</span> Баталгаажуулах</div>
  </div>

  <div id="alertBox"></div>

  <!-- STEP 1: SERVICE -->
  <div class="step-panel" id="panel-1">
    <h3>Үйлчилгээгээ сонгоно уу</h3>
    <div class="grid grid-3">
      <?php foreach ($services as $s): ?>
        <div class="card card-select" data-select-service
             data-id="<?= (int)$s['id'] ?>"
             data-name="<?= e($s['name']) ?>"
             data-duration="<?= (int)$s['duration'] ?>"
             data-price="<?= (float)$s['price'] ?>">
          <div class="meta"><?= (int)$s['duration'] ?> минут</div>
          <h3><?= e($s['name']) ?></h3>
          <div class="price"><?= formatMoney((float)$s['price']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- STEP 2: DOCTOR -->
  <div class="step-panel" id="panel-2" style="display:none;">
    <h3>Эмчээ сонгоно уу</h3>
    <div class="grid grid-3" id="doctorGrid"></div>
    <button class="btn btn-outline btn-sm" data-back="1" style="margin-top:16px;">← Буцах</button>
  </div>

  <!-- STEP 3: DATE + SLOT -->
  <div class="step-panel" id="panel-3" style="display:none;">
    <h3>Огноо, цаг сонгоно уу</h3>
    <div class="calendar-layout">
      <div id="calendarWidget" class="calendar"></div>
      <div id="slotArea" class="slot-area"></div>
    </div>
    <button class="btn btn-outline btn-sm" data-back="2" style="margin-top:16px;">← Буцах</button>
  </div>

  <!-- STEP 4: CONFIRM -->
  <div class="step-panel" id="panel-4" style="display:none;">
    <h3>Захиалгаа баталгаажуулна уу</h3>
    <div class="card" style="max-width:480px;">
      <p><strong>Үйлчилгээ:</strong> <span id="cfService"></span></p>
      <p><strong>Эмч:</strong> <span id="cfDoctor"></span></p>
      <p><strong>Огноо:</strong> <span id="cfDate"></span></p>
      <p><strong>Цаг:</strong> <span id="cfTime"></span></p>
      <p><strong>Үнэ:</strong> <span id="cfPrice"></span></p>

      <label>Овог нэр</label>
      <input type="text" id="nameInput" value="<?= e($_SESSION['patient_name'] ?? '') ?>" placeholder="Таны нэр" required>

      <div class="form-row">
        <div>
          <label>Утасны дугаар</label>
          <input type="tel" id="phoneInput" value="<?= e($_SESSION['patient_phone'] ?? '') ?>" placeholder="99001122" required>
        </div>
        <div>
          <label>Имэйл</label>
          <input type="email" id="emailInput" value="<?= isPatientLoggedIn() ? e($_SESSION['patient_email'] ?? '') : '' ?>" placeholder="you@example.com" required>
        </div>
      </div>
      <div class="field-hint">Захиалгын мэдээллийг энэ имэйлээр илгээнэ. Дараа дахин захиалахдаа мөн имэйлээ ашиглавал захиалгуудаа шалгах боломжтой.</div>

      <label>Нэмэлт тэмдэглэл (заавал биш)</label>
      <textarea id="noteInput" placeholder="Жишээ нь: гар өвдөж байгаа"></textarea>
      <button class="btn btn-accent btn-block" id="confirmBtn" style="margin-top:14px;">Захиалга баталгаажуулах</button>
    </div>
    <button class="btn btn-outline btn-sm" data-back="3" style="margin-top:16px;">← Буцах</button>
  </div>

</section>

<script>
  window.BOOKING_DATA = {
    services: <?= json_encode($services) ?>,
    doctors: <?= json_encode($doctors) ?>,
    serviceToDoctors: <?= json_encode($serviceToDoctors) ?>,
    doctorToServices: <?= json_encode($doctorToServices) ?>,
    preselectService: <?= (int)$preselectService ?>,
    preselectDoctor: <?= (int)$preselectDoctor ?>
  };
</script>
<script src="assets/js/booking.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
