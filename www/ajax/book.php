<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true) ?: [];

$doctorId  = (int)($data['doctor_id'] ?? 0);
$serviceId = (int)($data['service_id'] ?? 0);
$date      = $data['date'] ?? '';
$start     = $data['start'] ?? '';
$end       = $data['end'] ?? '';
$note      = trim($data['note'] ?? '');
$name      = trim($data['name'] ?? '');
$phone     = trim($data['phone'] ?? '');
$email     = trim($data['email'] ?? '');

if (!$doctorId || !$serviceId || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !$start || !$end) {
    http_response_code(400);
    echo json_encode(['error' => 'Дутуу мэдээлэл байна.']);
    exit;
}

if (isPatientLoggedIn()) {
    // Нэвтэрсэн хэрэглэгчийн хувьд өөрийнх нь бүртгэлийг ашиглана
    $patientId = (int)$_SESSION['patient_id'];
} else {
    if ($name === '' || $phone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Нэр, утас, имэйлээ зөв бөглөнө үү.']);
        exit;
    }
}

$db = getDB();

if (!isPatientLoggedIn()) {
    // Имэйлээр өвчтөнийг олох, байхгүй бол шинээр үүсгэх (зочноор захиалах урсгал)
    $stmt = $db->prepare("SELECT id FROM patients WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();

    if ($existing) {
        $patientId = (int)$existing['id'];
        $stmt = $db->prepare("UPDATE patients SET name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $patientId]);
    } else {
        $randomPassword = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
        $stmt = $db->prepare(
            "INSERT INTO patients (name, phone, email, password_hash, status) VALUES (?,?,?,?, 'active')"
        );
        $stmt->execute([$name, $phone, $email, $randomPassword]);
        $patientId = (int)$db->lastInsertId();
    }
}

// Тухайн слот одоо ч чөлөөтэй эсэхийг сервер талд дахин баталгаажуулна (race condition-оос сэргийлнэ)
$stmt = $db->prepare("SELECT duration FROM services WHERE id = ?");
$stmt->execute([$serviceId]);
$service = $stmt->fetch();
if (!$service) {
    http_response_code(404);
    echo json_encode(['error' => 'Үйлчилгээ олдсонгүй.']);
    exit;
}

$freeSlots = getAvailableSlots($db, $doctorId, $date, (int)$service['duration']);
$stillFree = false;
foreach ($freeSlots as $slot) {
    if ($slot['start'] === $start . ':00' || $slot['start'] === $start) {
        $stillFree = true;
        break;
    }
}

if (!$stillFree) {
    http_response_code(409);
    echo json_encode(['error' => 'Уучлаарай, энэ цагийг таны өмнө өөр хэрэглэгч захиалсан байна. Өөр цаг сонгоно уу.']);
    exit;
}

try {
    $stmt = $db->prepare(
        "INSERT INTO appointments (doctor_id, patient_id, service_id, appointment_date, start_time, end_time, status, note)
         VALUES (?,?,?,?,?,?, 'pending', ?)"
    );
    $stmt->execute([$doctorId, $patientId, $serviceId, $date, $start, $end, $note ?: null]);
    echo json_encode(['success' => true, 'appointment_id' => $db->lastInsertId()]);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        http_response_code(409);
        echo json_encode(['error' => 'Энэ цаг аль хэдийн захиалагдсан байна.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Захиалга үүсгэхэд алдаа гарлаа.']);
    }
}
