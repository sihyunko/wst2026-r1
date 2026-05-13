<?php
$cols      = 10;
$rows      = ceil($concert['total_seats'] / $cols);
$rowLabels = range('A', 'Z');

\Core\View::pushStyle(<<<'CSS'
.seat { width:36px; height:36px; border:1px solid #adb5bd; background:#fff; cursor:pointer; font-size:11px; border-radius:4px; }
.seat.selected { background:#212529; color:#fff; border-color:#212529; }
.seat.booked   { background:#dee2e6; color:#adb5bd; cursor:not-allowed; }
CSS);
?>

<h4 class="fw-bold mb-1"><?= htmlspecialchars($concert['title']) ?></h4>
<p class="text-muted mb-4">
  <?= date('Y-m-d H:i', strtotime($concert['date'])) ?> ·
  <?= htmlspecialchars($concert['venue']) ?> · 좌석당 <?= number_format($concert['price']) ?>원
</p>

<form id="bookingForm" method="post" action="<?= BASE_URL ?>/concerts/<?= $concert['id'] ?>/book">
<div class="row g-4">
  <!-- 좌석 배치도 -->
  <div class="col-md-8">
    <div class="card p-4">
      <p class="text-center text-muted small mb-3">— 무대 —</p>

      <?php for ($r = 0; $r < $rows; $r++): ?>
      <div class="d-flex align-items-center gap-1 mb-1">
        <span class="text-muted small me-1" style="width:16px;"><?= $rowLabels[$r] ?></span>
        <?php for ($c = 1; $c <= $cols; $c++): ?>
          <?php
            $seat     = $rowLabels[$r] . $c;
            $isBooked = in_array($seat, $bookedSeats);
          ?>
          <button type="button"
                  class="seat <?= $isBooked ? 'booked' : '' ?>"
                  data-seat="<?= $seat ?>"
                  <?= $isBooked ? 'disabled' : '' ?>><?= $c ?></button>
        <?php endfor; ?>
      </div>
      <?php endfor; ?>

      <!-- 범례 -->
      <div class="d-flex gap-3 mt-3 small text-muted">
        <span><span class="seat d-inline-block" style="width:16px;height:16px;vertical-align:middle;"></span> 선택 가능</span>
        <span><span class="seat selected d-inline-block" style="width:16px;height:16px;vertical-align:middle;"></span> 선택됨</span>
        <span><span class="seat booked d-inline-block" style="width:16px;height:16px;vertical-align:middle;"></span> 예매됨</span>
      </div>
    </div>
  </div>

  <!-- 예매 확인 -->
  <div class="col-md-4">
    <div class="card p-4">
      <h6 class="fw-bold mb-3">예매 확인</h6>
      <p class="small text-muted mb-1">선택한 좌석</p>
      <div id="selectedSeats" class="mb-3 small">없음</div>
      <hr>
      <div class="d-flex justify-content-between mb-3">
        <span class="small">총 금액</span>
        <span class="fw-bold" id="totalPrice">0원</span>
      </div>
      <button type="submit" id="submitBtn" class="btn btn-dark w-100" disabled>예매 확정</button>
    </div>
  </div>
</div>
</form>

<?php \Core\View::pushScript(<<<'JS'
const selected = [];
const PRICE = 5000;

document.querySelectorAll('.seat:not(.booked)').forEach(btn => {
  btn.addEventListener('click', function () {
    const seat = this.dataset.seat;
    const idx  = selected.indexOf(seat);
    if (idx === -1) {
      selected.push(seat);
      this.classList.add('selected');
    } else {
      selected.splice(idx, 1);
      this.classList.remove('selected');
    }
    updatePanel();
  });
});

function updatePanel() {
  document.getElementById('selectedSeats').textContent =
    selected.length ? selected.join(', ') : '없음';
  document.getElementById('totalPrice').textContent =
    (selected.length * PRICE).toLocaleString() + '원';
  document.getElementById('submitBtn').disabled = selected.length === 0;

  const form = document.getElementById('bookingForm');
  form.querySelectorAll('input[name="seats[]"]').forEach(i => i.remove());
  selected.forEach(seat => {
    const inp = document.createElement('input');
    inp.type  = 'hidden';
    inp.name  = 'seats[]';
    inp.value = seat;
    form.appendChild(inp);
  });
}
JS); ?>