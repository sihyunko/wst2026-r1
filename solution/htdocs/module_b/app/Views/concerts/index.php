<!-- 검색/필터/정렬 -->
<form method="get" action="<?= BASE_URL ?>/">
  <div class="row g-2 mb-4">
    <div class="col-md-4">
      <input type="text" name="search" class="form-control" placeholder="공연명 검색"
             value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-3">
      <select name="venue" class="form-select">
        <option value="">장소 전체</option>
        <?php foreach ($cities as $city): ?>
          <option value="<?= htmlspecialchars($city) ?>"
                  <?= $venue === $city ? 'selected' : '' ?>>
            <?= htmlspecialchars($city) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="order" class="form-select">
        <option value="desc" <?= $order === 'desc' ? 'selected' : '' ?>>날짜 내림차순</option>
        <option value="asc"  <?= $order === 'asc'  ? 'selected' : '' ?>>날짜 오름차순</option>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-dark w-100">검색</button>
    </div>
  </div>
</form>

<!-- 공연 목록 -->
<div class="row g-4">
  <?php foreach ($concerts as $c): ?>
  <div class="col-md-4">
    <a href="<?= BASE_URL ?>/concerts/<?= $c['id'] ?>" class="text-decoration-none text-dark">
      <div class="card h-100">
        <img src="<?= BASE_URL ?>/posters/<?= htmlspecialchars($c['poster']) ?>"
             class="card-img-top" style="height:200px; object-fit:cover;"
             alt="<?= htmlspecialchars($c['title']) ?>">
        <div class="card-body">
          <h6 class="card-title fw-bold">
            <?= htmlspecialchars($c['title']) ?>
            <?php if ($c['remaining'] <= 0): ?>
              <span class="badge bg-danger ms-1 small">매진</span>
            <?php endif; ?>
          </h6>
          <p class="card-text small text-muted mb-1">📅 <?= date('Y-m-d H:i', strtotime($c['date'])) ?></p>
          <p class="card-text small text-muted mb-1">📍 <?= htmlspecialchars($c['venue']) ?></p>
          <p class="card-text small text-muted mb-1">💰 <?= number_format($c['price']) ?>원</p>
          <p class="card-text small text-muted">🎟 잔여석 <?= $c['remaining'] ?> / <?= $c['total_seats'] ?></p>
        </div>
      </div>
    </a>
  </div>
  <?php endforeach; ?>

  <?php if (empty($concerts)): ?>
    <div class="col-12 text-center text-muted py-5">검색 결과가 없습니다.</div>
  <?php endif; ?>
</div>
