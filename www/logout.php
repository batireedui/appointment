<?php
require_once __DIR__ . '/includes/functions.php';
unset($_SESSION['patient_id'], $_SESSION['patient_name']);
redirect('index.php');
