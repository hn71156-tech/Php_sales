<?php
declare(strict_types=1);

function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function redirect(string $path): never {
    header('Location: ' . BASE_URL . $path);
    exit;
}
function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Phiên biểu mẫu không hợp lệ. Hãy tải lại trang và thử lại.');
    }
}
function require_login(): void {
    if (empty($_SESSION['user'])) {
        flash('warning', 'Vui lòng đăng nhập để tiếp tục.');
        redirect('/login.php');
    }
}
function require_role(string ...$roles): void {
    require_login();
    if (!in_array($_SESSION['user']['role'], $roles, true)) {
        http_response_code(403);
        exit('Bạn không có quyền truy cập chức năng này.');
    }
}
function current_user(): array { return $_SESSION['user'] ?? []; }
function money(mixed $amount): string { return number_format((float)$amount, 0, ',', '.') . ' ₫'; }
function set_flash_from_session(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}
