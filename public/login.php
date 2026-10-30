<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers.php';
if (!empty($_SESSION['user'])) redirect('/index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $stmt = db()->prepare('SELECT id,username,password_hash,full_name,role,is_active FROM users WHERE username=? LIMIT 1');
    $stmt->execute([$username]); $user = $stmt->fetch();
    if ($user && (int)$user['is_active'] === 1 && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>(int)$user['id'],'username'=>$user['username'],'full_name'=>$user['full_name'],'role'=>$user['role']];
        redirect('/index.php');
    }
    $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
}
$pageTitle='Đăng nhập';
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Đăng nhập · StockPro</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="<?= e(BASE_URL) ?>/assets/app.css" rel="stylesheet"></head><body>
<div class="login-wrap px-3"><div class="card"><div class="card-body p-4 p-md-5"><div class="login-brand mb-1">📦 StockPro</div><p class="text-secondary mb-4">Quản lý kho và bán hàng</p>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="mb-3"><label class="form-label">Tên đăng nhập</label><input name="username" class="form-control form-control-lg" required autofocus></div><div class="mb-4"><label class="form-label">Mật khẩu</label><input type="password" name="password" class="form-control form-control-lg" required></div><button class="btn btn-primary btn-lg w-100">Đăng nhập</button></form>
<div class="small text-secondary mt-4">Tài khoản demo sau khi import SQL: <b>admin</b> / <b>password</b></div></div></div><p class="text-center text-secondary small mt-3">Dùng dữ liệu mẫu để trình diễn tại máy local.</p></div></body></html>
