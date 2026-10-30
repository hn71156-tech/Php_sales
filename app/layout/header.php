<?php
require_once __DIR__ . '/../helpers.php';
$user = current_user();
$flashMessage = set_flash_from_session();
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> · <?= e(APP_NAME) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= e(BASE_URL) ?>/assets/app.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
 <div class="container-fluid">
  <a class="navbar-brand fw-bold" href="<?= e(BASE_URL) ?>/index.php">📦 StockPro</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain"><span class="navbar-toggler-icon"></span></button>
  <div class="collapse navbar-collapse" id="navMain">
   <?php if ($user): ?>
   <ul class="navbar-nav me-auto">
    <li class="nav-item"><a class="nav-link" href="<?= e(BASE_URL) ?>/index.php">Tổng quan</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= e(BASE_URL) ?>/products.php">Sản phẩm</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= e(BASE_URL) ?>/categories.php">Danh mục</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= e(BASE_URL) ?>/suppliers.php">Nhà cung cấp</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= e(BASE_URL) ?>/purchases.php">Nhập kho</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= e(BASE_URL) ?>/orders.php">Đơn hàng</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= e(BASE_URL) ?>/reports.php">Báo cáo</a></li>
    <?php if ($user['role'] === 'admin'): ?><li class="nav-item"><a class="nav-link" href="<?= e(BASE_URL) ?>/users.php">Tài khoản</a></li><?php endif; ?>
   </ul>
   <span class="navbar-text me-3"><?= e($user['full_name']) ?> · <?= e($user['role']) ?></span>
   <form method="post" action="<?= e(BASE_URL) ?>/logout.php" class="d-flex">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <button class="btn btn-outline-light btn-sm">Đăng xuất</button>
   </form>
   <?php endif; ?>
  </div>
 </div>
</nav>
<main class="container-fluid px-3 px-lg-4 py-4">
<?php if ($flashMessage): ?>
<div class="alert alert-<?= e($flashMessage['type']) ?> alert-dismissible fade show" role="alert">
 <?= e($flashMessage['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
