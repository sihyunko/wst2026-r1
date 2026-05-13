<?php \Core\View::pushStyle(<<<'CSS'
.ticket { border:2px dashed #adb5bd; border-radius:12px; padding:24px; background:#fff; position:relative; }
.ticket::before { content:''; position:absolute; left:-14px; top:50%; transform:translateY(-50%); width:24px; height:24px; background:#f8f9fa; border-radius:50%; border:2px dashed #adb5bd; }
.ticket::after  { content:''; position:absolute; right:-14px; top:50%; transform:translateY(-50%); width:24px; height:24px; background:#f8f9fa; border-radius:50%; border:2px dashed #adb5bd; }
.ticket-seat { font-size:2rem; font-weight:bold; }
CSS); ?>

<div class="d-flex align-items-center gap-3 mb-4">
  <a href="<?= BASE_URL ?>/bookings" class="btn btn-outline-secondary btn-sm">← 뒤로가기</a>
  <h4 class="fw-bold mb-0">예매 상세</h4>
</div>

<div class="row g-4">
  <?php foreach ($seats as $seat): ?>
  <div class="col-md-4">
    <div class="ticket">
      <div class="text-muted small mb-1">TicketBox</div>
      <h5 class="fw-bold mb-3"><?= htmlspecialchars($booking['title']) ?></h5>
      <hr>
      <div class="d-flex justify-content-between mb-1">
        <span class="text-muted small">날짜/시간</span>
        <span class="small"><?= date('Y-m-d H:i', strtotime($booking['date'])) ?></span>
      </div>
      <div class="d-flex justify-content-between mb-1">
        <span class="text-muted small">장소</span>
        <span class="small"><?= htmlspecialchars($booking['venue']) ?></span>
      </div>
      <div class="d-flex justify-content-between mb-1">
        <span class="text-muted small">예매 일시</span>
        <span class="small"><?= date('Y-m-d H:i', strtotime($booking['booked_at'])) ?></span>
      </div>
      <div class="d-flex justify-content-between mb-1">
        <span class="text-muted small">가격</span>
        <span class="small"><?= number_format($booking['price']) ?>원</span>
      </div>
      <hr>
      <div class="text-center ticket-seat"><?= htmlspecialchars($seat) ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>