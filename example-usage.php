<?php
require_once 'NonceManager.php';

$nonce = new NonceManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($nonce->verify_nonce($_POST['_nonce'], 'register_user')) {
        echo '✅ تم التحقق من الرمز بنجاح';
    } else {
        echo '⛔️ رمز التحقق غير صالح أو منتهي';
    }
    exit;
}

$token = $nonce->create_nonce('register_user');
?>

<form method="post">
    <input type="hidden" name="_nonce" value="<?= htmlspecialchars($token) ?>">
    <button type="submit">تسجيل</button>
</form>
