<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function isPatientLoggedIn(): bool
{
    return !empty($_SESSION['patient_id']);
}

function isAdminLoggedIn(): bool
{
    return !empty($_SESSION['admin_id']);
}

function requirePatientLogin(): void
{
    if (!isPatientLoggedIn()) {
        redirect('login.php');
    }
}

function requireAdminLogin(): void
{
    if (!isAdminLoggedIn()) {
        redirect('login.php');
    }
}

function flash(string $key, ?string $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

const DAY_NAMES_MN = ['Ням', 'Даваа', 'Мягмар', 'Лхагва', 'Пүрэв', 'Баасан', 'Бямба'];

const STATUS_LABELS_MN = [
    'pending'   => 'Хүлээгдэж буй',
    'confirmed' => 'Баталгаажсан',
    'completed' => 'Дууссан',
    'cancelled' => 'Цуцалсан',
    'no_show'   => 'Ирээгүй',
];

function statusLabel(string $status): string
{
    return STATUS_LABELS_MN[$status] ?? $status;
}

/**
 * Тухайн эмчийн өгөгдсөн огноон дахь боломжит цагуудыг тооцоолно.
 * Байнгын хуваарь + онцгой өөрчлөлт + одоо байгаа захиалгуудыг харгалзана.
 */
function getAvailableSlots(PDO $db, int $doctorId, string $date, int $durationMinutes): array
{
    $dayOfWeek = (int) date('w', strtotime($date));

    // Тухайн өдөр амарч байгаа эсэхийг шалгах
    $stmt = $db->prepare(
        "SELECT * FROM schedule_exceptions WHERE doctor_id = ? AND exception_date = ?"
    );
    $stmt->execute([$doctorId, $date]);
    $exceptions = $stmt->fetchAll();

    foreach ($exceptions as $ex) {
        if ($ex['type'] === 'day_off' && empty($ex['start_time'])) {
            return []; // Бүтэн өдөр амарна
        }
    }

    // Байнгын хуваарь
    $stmt = $db->prepare(
        "SELECT start_time, end_time FROM doctor_schedules
         WHERE doctor_id = ? AND day_of_week = ? AND status = 'active'"
    );
    $stmt->execute([$doctorId, $dayOfWeek]);
    $ranges = $stmt->fetchAll();

    // Онцгой нэмэлт цагийг хуваарьт нэмэх
    foreach ($exceptions as $ex) {
        if ($ex['type'] === 'extra_hours' && $ex['start_time'] && $ex['end_time']) {
            $ranges[] = ['start_time' => $ex['start_time'], 'end_time' => $ex['end_time']];
        }
        if ($ex['type'] === 'day_off' && $ex['start_time'] && $ex['end_time']) {
            // Тухайн хугацаанд л амарна - дараа нь ranges-аас тухайн хэсгийг хасна
        }
    }

    if (empty($ranges)) {
        return [];
    }

    // Захиалагдсан цагууд
    $stmt = $db->prepare(
        "SELECT start_time, end_time FROM appointments
         WHERE doctor_id = ? AND appointment_date = ? AND status IN ('pending','confirmed')"
    );
    $stmt->execute([$doctorId, $date]);
    $booked = $stmt->fetchAll();

    $isToday = ($date === date('Y-m-d'));
    $nowMinutes = (int) date('H') * 60 + (int) date('i');

    $slots = [];
    foreach ($ranges as $range) {
        $start = strtotime($date . ' ' . $range['start_time']);
        $end   = strtotime($date . ' ' . $range['end_time']);
        $cursor = $start;

        while ($cursor + $durationMinutes * 60 <= $end) {
            $slotStart = date('H:i:s', $cursor);
            $slotEndTs = $cursor + $durationMinutes * 60;
            $slotEnd   = date('H:i:s', $slotEndTs);

            $slotMinutes = (int) date('H', $cursor) * 60 + (int) date('i', $cursor);

            $conflict = false;
            foreach ($booked as $b) {
                if ($slotStart < $b['end_time'] && $slotEnd > $b['start_time']) {
                    $conflict = true;
                    break;
                }
            }

            // Өнөөдрийн хувьд өнгөрсөн цагийг санал болгохгүй
            if ($isToday && $slotMinutes <= $nowMinutes) {
                $conflict = true;
            }

            if (!$conflict) {
                $slots[] = ['start' => $slotStart, 'end' => $slotEnd];
            }

            $cursor += $durationMinutes * 60;
        }
    }

    return $slots;
}

function getServicePrice(PDO $db, int $doctorId, int $serviceId): float
{
    $stmt = $db->prepare(
        "SELECT ds.price AS ds_price, s.price AS s_price
         FROM services s
         LEFT JOIN doctor_services ds ON ds.service_id = s.id AND ds.doctor_id = ?
         WHERE s.id = ?"
    );
    $stmt->execute([$doctorId, $serviceId]);
    $row = $stmt->fetch();
    if (!$row) {
        return 0.0;
    }
    return $row['ds_price'] !== null ? (float) $row['ds_price'] : (float) $row['s_price'];
}

function formatMoney(float $amount): string
{
    return number_format($amount, 0, '.', ',') . '₮';
}
