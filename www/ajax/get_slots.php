<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');

$doctorId  = (int)($_GET['doctor_id'] ?? 0);
$serviceId = (int)($_GET['service_id'] ?? 0);
$date      = $_GET['date'] ?? '';

if (!$doctorId || !$serviceId || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    http_response_code(400);
    echo json_encode(['error' => 'Дутуу параметр']);
    exit;
}

if ($date < date('Y-m-d')) {
    echo json_encode(['slots' => []]);
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT duration FROM services WHERE id = ?");
$stmt->execute([$serviceId]);
$service = $stmt->fetch();
if (!$service) {
    http_response_code(404);
    echo json_encode(['error' => 'Үйлчилгээ олдсонгүй']);
    exit;
}

$slots = getAvailableSlots($db, $doctorId, $date, (int)$service['duration']);
echo json_encode(['slots' => $slots]);
