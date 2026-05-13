<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row justify-content-center">
  <div class="col-md-5">
    <h4 class="mb-4">로그인</h4>
    <form method="post" action="<?= BASE_URL ?>/login">
      <div class="mb-3">
        <label class="form-label">이메일</label>
        <input type="email" name="email" class="form-control" placeholder="이메일"
               value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">비밀번호</label>
        <input type="password" name="password" class="form-control" placeholder="비밀번호" required>
      </div>
      <button type="submit" class="btn btn-dark w-100">로그인</button>
    </form>
    <p class="text-center mt-3 small">계정이 없으신가요? <a href="<?= BASE_URL ?>/register">회원가입</a></p>
  </div>
</div>