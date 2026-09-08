<?php
$pageTitle = 'Хуваарь';
require_once __DIR__ . '/includes/admin-header.php';
$db = getDB();

$doctors = $db->query("SELECT * FROM doctors WHERE status='active' ORDER BY name")->fetchAll();
$doctorId = (int)($_GET['doctor_id'] ?? ($doctors[0]['id'] ?? 0));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_schedule') {
        $stmt = $db->prepare(
            "INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time) VALUES (?,?,?,?)"
        );
        $stmt->execute([(int)$_POST['doctor_id'], (int)$_POST['day_of_week'], $_POST['start_time'], $_POST['end_time']]);
    } elseif ($action === 'remove_schedule') {
        $stmt = $db->prepare("DELETE FROM doctor_schedules WHERE id = ?");
        $stmt->execute([(int)$_POST['id']]);
    } elseif ($action === 'add_exception') {
        $stmt = $db->prepare(
            "INSERT INTO schedule_exceptions (doctor_id, exception_date, start_time, end_time, type, reason) VALUES (?,?,?,?,?,?)"
        );
        $stmt->execute([
            (int)$_POST['doctor_id'],
            $_POST['exception_date'],
            $_POST['ex_start_time'] ?: null,
            $_POST['ex_end_time'] ?: null,
            $_POST['type'],
            trim($_POST['reason'] ?? '') ?: null,
        ]);
    } elseif ($action === 'remove_exception') {
        $stmt = $db->prepare("DELETE FROM schedule_exceptions WHERE id = ?");
        $stmt->execute([(int)$_POST['id']]);
    }
    redirect('schedules.php?doctor_id=' . $doctorId);
}

$stmt = $db->prepare("SELECT * FROM doctor_schedules WHERE doctor_id = ? ORDER BY day_of_week, start_time");
$stmt->execute([$doctorId]);
$schedules = $stmt->fetchAll();

$stmt = $db->prepare("SELECT * FROM schedule_exceptions WHERE doctor_id = ? ORDER BY exception_date DESC");
$stmt->execute([$doctorId]);
$exceptions = $stmt->fetchAll();
?>
<div class="block-head"><h2>Эмчийн хуваарь</h2></div>

<form method="get" style="margin-bottom:20px; max-width:300px;">
  <label>Эмч сонгох</label>
  <select name="doctor_id" onchange="this.form.submit()">
    <?php foreach ($doctors as $d): ?>
      <option value="<?= (int)$d['id'] ?>" <?= $doctorId===(int)$d['id']?'selected':'' ?>><?= e($d['name']) ?></option>
    <?php endforeach; ?>
  </select>
</form>

<div class="grid grid-2" style="align-items:start;">
  <div>
    <h3>Долоо хоногийн байнгын хуваарь</h3>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Гараг</th><th>Эхлэх</th><th>Дуусах</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($schedules as $s): ?>
          <tr>
            <td><?= DAY_NAMES_MN[(int)$s['day_of_week']] ?></td>
            <td><?= substr($s['start_time'],0,5) ?></td>
            <td><?= substr($s['end_time'],0,5) ?></td>
            <td>
              <form method="post" onsubmit="return confirm('Устгах уу?');">
                <input type="hidden" name="action" value="remove_schedule">
                <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                <button class="btn btn-danger btn-sm">Устгах</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$schedules): ?><tr><td colspan="4" class="muted" style="text-align:center;">Хуваарь алга байна.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="card" style="margin-top:16px;">
      <h3>Хуваарь нэмэх</h3>
      <form method="post">
        <input type="hidden" name="action" value="add_schedule">
        <input type="hidden" name="doctor_id" value="<?= $doctorId ?>">
        <label>Гараг</label>
        <select name="day_of_week" required>
          <?php foreach (DAY_NAMES_MN as $i => $name): ?><option value="<?= $i ?>"><?= $name ?></option><?php endforeach; ?>
        </select>
        <div class="form-row">
          <div><label>Эхлэх цаг</label><input type="time" name="start_time" required></div>
          <div><label>Дуусах цаг</label><input type="time" name="end_time" required></div>
        </div>
        <button type="submit" class="btn btn-primary btn-block" style="margin-top:16px;">Нэмэх</button>
      </form>
    </div>
  </div>

  <div>
    <h3>Онцгой өдрүүд (амралт / нэмэлт цаг)</h3>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Огноо</th><th>Төрөл</th><th>Цаг</th><th>Шалтгаан</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($exceptions as $ex): ?>
          <tr>
            <td><?= e($ex['exception_date']) ?></td>
            <td><?= $ex['type']==='day_off' ? 'Амралт' : 'Нэмэлт цаг' ?></td>
            <td><?= $ex['start_time'] ? substr($ex['start_time'],0,5).'-'.substr($ex['end_time'],0,5) : 'Бүтэн өдөр' ?></td>
            <td><?= e($ex['reason'] ?? '') ?></td>
            <td>
              <form method="post" onsubmit="return confirm('Устгах уу?');">
                <input type="hidden" name="action" value="remove_exception">
                <input type="hidden" name="id" value="<?= (int)$ex['id'] ?>">
                <button class="btn btn-danger btn-sm">Устгах</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$exceptions): ?><tr><td colspan="5" class="muted" style="text-align:center;">Онцгой өдөр алга байна.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="card" style="margin-top:16px;">
      <h3>Онцгой өдөр нэмэх</h3>
      <form method="post">
        <input type="hidden" name="action" value="add_exception">
        <input type="hidden" name="doctor_id" value="<?= $doctorId ?>">
        <label>Огноо</label>
        <input type="date" name="exception_date" required>
        <label>Төрөл</label>
        <select name="type">
          <option value="day_off">Амралт</option>
          <option value="extra_hours">Нэмэлт цаг</option>
        </select>
        <div class="form-row">
          <div><label>Эхлэх цаг (заавал биш)</label><input type="time" name="ex_start_time"></div>
          <div><label>Дуусах цаг (заавал биш)</label><input type="time" name="ex_end_time"></div>
        </div>
        <div class="field-hint">Амралтын хувьд цаг хоосон бол бүтэн өдөр амарна гэж тооцно.</div>
        <label>Шалтгаан</label>
        <input type="text" name="reason" placeholder="Жишээ: чөлөө, семинар">
        <button type="submit" class="btn btn-primary btn-block" style="margin-top:16px;">Нэмэх</button>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
