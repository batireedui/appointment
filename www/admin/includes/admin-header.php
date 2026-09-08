<?php
require_once __DIR__ . '/../../includes/functions.php';
requireAdminLogin();
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>Админ самбар</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <aside class="admin-sidebar">
    <a href="index.php" class="brand">Тайван <small>админ</small></a>
    <nav>
      <a href="index.php" class="<?= $currentPage==='index.php'?'active':'' ?>">Хянах самбар</a>
      <a href="appointments.php" class="<?= $currentPage==='appointments.php'?'active':'' ?>">Захиалгууд</a>
      <a href="doctors.php" class="<?= $currentPage==='doctors.php'?'active':'' ?>">Эмч нар</a>
      <a href="services.php" class="<?= $currentPage==='services.php'?'active':'' ?>">Үйлчилгээ</a>
      <a href="doctor-services.php" class="<?= $currentPage==='doctor-services.php'?'active':'' ?>">Эмч ↔ Үйлчилгээ</a>
      <a href="schedules.php" class="<?= $currentPage==='schedules.php'?'active':'' ?>">Хуваарь</a>
      <a href="patients.php" class="<?= $currentPage==='patients.php'?'active':'' ?>">Өвчтөнүүд</a>
    </nav>
  </aside>
  <main class="admin-main">
    <div class="admin-topbar">
      <div></div>
      <div class="actions-row" style="align-items:center;">
        <span class="muted"><?= e($_SESSION['admin_name']) ?> (<?= e($_SESSION['admin_role']) ?>)</span>
        <a href="logout.php" class="btn btn-outline btn-sm">Гарах</a>
      </div>
    </div>
