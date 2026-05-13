<?php
$isEnded    = new DateTime() > new DateTime($concert['date']);
$isSoldOut  = $concert['remaining'] <= 0;
?>

<div class="row g-4">
  <!-- 포스터 -->
  <div class="col-md-4">
    <img src="<?= BASE_URL ?>/posters/<?= htmlspecialchars($concert['poster']) ?>"
         class="img-fluid rounded" style="width:100%; height:320px; object-fit:cover;"
         alt="<?= htmlspecialchars($concert['title']) ?>">
  </div>

  <!-- 공연 정보 -->
  <div class="col-md-8">
    <h3 class="fw-bold mb-3"><?= htmlspecialchars($concert['title']) ?></h3>
    <table class="table table-borderless">
      <tbody>
        <tr>
          <th class="text-muted" style="width:120px;">날짜/시간</th>
          <td><?= date('Y-m-d H:i', strtotime($concert['date'])) ?></td>
        </tr>
        <tr>
          <th class="text-muted">장소</th>
          <td><?= htmlspecialchars($concert['venue']) ?></td>
        </tr>
        <tr>
          <th class="text-muted">티켓 가격</th>
          <td><?= number_format($concert['price']) ?>원</td>
        </tr>
        <tr>
          <th class="text-muted">잔여석</th>
          <td><?= $concert['remaining'] ?> / <?= $concert['total_seats'] ?></td>
        </tr>
      </tbody>
    </table>
    <p class="text-muted"><?= htmlspecialchars($concert['description']) ?></p>

    <?php if ($isEnded): ?>
      <button class="btn btn-secondary mt-2" disabled>공연 종료</button>
    <?php elseif ($isSoldOut): ?>
      <button class="btn btn-secondary mt-2" disabled>매진</button>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/concerts/<?= $concert['id'] ?>/book" class="btn btn-dark mt-2">예매하기</a>
    <?php endif; ?>
  </div>
</div>