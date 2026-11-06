<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['httponly'=>true, 'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'samesite'=>'Lax']);
    session_start();
}
function db(): PDO {
    static $pdo = null;
    if (!$pdo) {
        $dsn = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
    }
    return $pdo;
}
function h(?string $s): string { return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path=''): string { return BASE_URL . '/' . ltrim($path,'/'); }
function redirect(string $path): never { header('Location: '.url($path)); exit; }
function user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(?string $role=null): array {
    $u=user();
    if (!$u) redirect('login.php');
    $q=db()->prepare('SELECT id,name,email,role,active FROM users WHERE id=?');
    $q->execute([$u['id']]); $fresh=$q->fetch();
    if (!$fresh || !$fresh['active']) { unset($_SESSION['user']); redirect('login.php'); }
    if ($role && $fresh['role'] !== $role) { http_response_code(403); exit('Accès interdit'); }
    $_SESSION['user']=$fresh;
    return $fresh;
}
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="'.h(csrf()).'">'; }
function verify_csrf(): void { if (!hash_equals(csrf(), (string)($_POST['csrf']??''))) { http_response_code(419); exit('Session expirée. Rechargez la page.'); } }
function flash(string $message): void { $_SESSION['flash']=$message; }
function consume_flash(): ?string { $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }
function log_action(?array $actor, string $action, ?string $type=null, ?int $id=null, ?string $details=null): void {
 $q=db()->prepare('INSERT INTO activity_logs(actor_id,actor_email,action,target_type,target_id,details) VALUES(?,?,?,?,?,?)');
 $q->execute([$actor['id']??null,$actor['email']??null,$action,$type,$id,$details]);
}
function notify(int $id,string $message,string $link='student/notifications.php'): void {
 $q=db()->prepare('INSERT INTO notifications(user_id,message,link) VALUES(?,?,?)'); $q->execute([$id,$message,$link]);
}
