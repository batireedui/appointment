<?php
if (!isset($_SESSION['user_id'])) {
  redirect("/login");
} else {
  $user_id = $_SESSION['user_id'];
  $user_name = $_SESSION['user_name'];
}
$medeeMenuId = "2";
$medeeCateId = "4";
$onclohCateId = "3";
$salbarCateId = "2";
$userMenuId = "1";
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?= $webname ?> </title>
  <link rel="icon" type="image/x-icon" href="<?= $favi ?>" />
  <meta name="description" content='Өвөрхангай ПК' />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/styles/main.css" />
  <style>
    .editcell {
      background-color: #dddddd;
      padding: 0px 10px;
      border-radius: 5px;
    }
  </style>