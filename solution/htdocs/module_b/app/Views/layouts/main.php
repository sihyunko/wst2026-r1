<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'TicketBox') ?></title>
  <link href="<?= BASE_URL ?>/css/bootstrap.min.css" rel="stylesheet">
  <?php $style = \Core\View::flushStyle(); if ($style): ?>
  <style><?= $style ?></style>
  <?php endif; ?>
</head>
<body class="bg-light">

<?php
// 현재 경로 (BASE_URL prefix 제거)
$_scriptBase  = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$_currentPath = '/' . ltrim(substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen($_scriptBase)), '/');
$_onBookings  = str_starts_with($_currentPath, '/bookings');
?>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/">TicketBox</a>
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?= !$_onBookings ? 'active' : '' ?>"
             href="<?= BASE_URL ?>/">공연 목록</a>
        </li>
        <?php if (isset($_SESSION['user_id'])): ?>
        <li class="nav-item">
          <a class="nav-link <?= $_onBookings ? 'active' : '' ?>"
             href="<?= BASE_URL ?>/bookings">예매 내역</a>
        </li>
        <?php endif; ?>
      </ul>
      <div class="d-flex gap-2 align-items-center">
        <?php if (isset($_SESSION['user_id'])): ?>
          <span class="navbar-text text-white me-1"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
          <a href="<?= BASE_URL ?>/logout" class="btn btn-outline-light btn-sm">로그아웃</a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/login"    class="btn btn-outline-light btn-sm">로그인</a>
          <a href="<?= BASE_URL ?>/register" class="btn btn-light btn-sm">회원가입</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <?php if (isset($_SESSION['flash'])): ?>
    <?php $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
    <div class="container pt-3">
      <div class="alert alert-<?= htmlspecialchars($f['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($f['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  <?php endif; ?>

  <div class="container py-5">
    <?= $content ?>
  </div>

  <script src="<?= BASE_URL ?>/js/bootstrap.bundle.min.js"></script>
  <?php $script = \Core\View::flushScript(); if ($script): ?>
  <script><?= $script ?></script>
  <?php endif; ?>
</body>
</html>
