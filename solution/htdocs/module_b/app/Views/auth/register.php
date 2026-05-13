<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="row justify-content-center">
  <div class="col-md-5">
    <h4 class="mb-4">회원가입</h4>
    <form method="post" action="<?= BASE_URL ?>/register">
      <div class="mb-3">
        <label class="form-label">이름</label>
        <input type="text" name="name" class="form-control" placeholder="이름"
               value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">이메일</label>
        <input type="email" name="email" class="form-control" placeholder="이메일"
               value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">비밀번호</label>
        <div class="input-group">
          <input type="password" name="password" id="password" class="form-control"
                 placeholder="비밀번호 (4자 이상)" required>
          <button class="btn btn-outline-secondary" type="button" id="togglePassword">보기</button>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">전화번호</label>
        <input type="text" name="phone" class="form-control" placeholder="전화번호 (숫자만)"
               value="<?= htmlspecialchars($old['phone'] ?? '') ?>" required>
      </div>
      <button type="submit" class="btn btn-dark w-100">회원가입</button>
    </form>
    <p class="text-center mt-3 small">이미 계정이 있으신가요? <a href="<?= BASE_URL ?>/login">로그인</a></p>
  </div>
</div>

<?php \Core\View::pushScript(<<<'JS'
document.getElementById('togglePassword').addEventListener('click', function () {
  const pw = document.getElementById('password');
  const hidden = pw.type === 'password';
  pw.type = hidden ? 'text' : 'password';
  this.textContent = hidden ? '숨기기' : '보기';
});
JS); ?>