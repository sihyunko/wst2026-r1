<h4 class="fw-bold mb-4">예매 내역</h4>

<?php if (empty($bookings)): ?>
  <p class="text-muted">예매 내역이 없습니다.</p>
<?php else: ?>
<div class="table-responsive">
  <table class="table table-hover align-middle">
    <thead class="table-dark">
      <tr>
        <th>공연명</th>
        <th>날짜/시간</th>
        <th>장소</th>
        <th>예매 좌석 수</th>
        <th>결제 금액</th>
        <th>예매 일시</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($bookings as $b): ?>
        <?php $isPast = new DateTime() > new DateTime($b['date']); ?>
        <tr class="<?= (int)$b['id'] === $newId ? 'table-warning' : '' ?>"
            style="cursor:pointer;"
            onclick="location.href='<?= BASE_URL ?>/bookings/<?= $b['id'] ?>'">
          <td><?= htmlspecialchars($b['title']) ?></td>
          <td><?= date('Y-m-d H:i', strtotime($b['date'])) ?></td>
          <td><?= htmlspecialchars($b['venue']) ?></td>
          <td><?= $b['seat_count'] ?>석</td>
          <td><?= number_format($b['total_price']) ?>원</td>
          <td><?= date('Y-m-d H:i', strtotime($b['booked_at'])) ?></td>
          <td>
            <?php if (!$isPast): ?>
              <form method="post"
                    action="<?= BASE_URL ?>/bookings/<?= $b['id'] ?>/cancel"
                    onsubmit="return confirm('예매를 취소하시겠습니까?')">
                <button type="submit"
                        class="btn btn-outline-danger btn-sm"
                        onclick="event.stopPropagation()">취소</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>