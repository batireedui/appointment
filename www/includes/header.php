<?php
require_once __DIR__ . '/functions.php';
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>Тайван эмнэлэг</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="bar">
    <a href="index.php" class="brand">Тайван <small>эмнэлэг</small></a>
    <nav class="main-nav">
      <a href="index.php" class="<?= $currentPage==='index.php'?'active':'' ?>">Нүүр</a>
      <a href="doctors.php" class="<?= $currentPage==='doctors.php'?'active':'' ?>">Эмч нар</a>
      <a href="services.php" class="<?= $currentPage==='services.php'?'active':'' ?>">Үйлчилгээ</a>
      <?php if (isPatientLoggedIn()): ?>
        <a href="my-appointments.php" class="<?= $currentPage==='my-appointments.php'?'active':'' ?>">Миний захиалга</a>
      <?php else: ?>
        <a href="find-appointments.php" class="<?= $currentPage==='find-appointments.php'?'active':'' ?>">Захиалгаа шалгах</a>
      <?php endif; ?>
    </nav>
    <div class="nav-actions">
      <?php if (isPatientLoggedIn()): ?>
        <span class="muted"><?= e($_SESSION['patient_name']) ?></span>
        <a href="logout.php" class="btn btn-outline btn-sm">Гарах</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-outline btn-sm">Нэвтрэх</a>
        <a href="register.php" class="btn btn-primary btn-sm">Бүртгүүлэх</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main>
