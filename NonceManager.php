<?php
class NonceManager {
    private $secret_key = 'change_this_secret';
    private $nonce_lifetime = 1800; // 30 دقيقة

    public function create_nonce($action) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $user_token = session_id();
        $timestamp = time();
        $nonce = hash_hmac('sha256', $action . $user_token . $timestamp, $this->secret_key);

        return base64_encode(json_encode([
            'nonce' => $nonce,
            'time' => $timestamp,
            'action' => $action
        ]));
    }

    public function verify_nonce($encoded_nonce, $action) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $user_token = session_id();

        $data = json_decode(base64_decode($encoded_nonce), true);

        if (!$data || !isset($data['nonce'], $data['time'], $data['action'])) {
            return false;
        }

        if ($data['action'] !== $action) {
            return false;
        }

        if (time() - $data['time'] > $this->nonce_lifetime) {
            return false;
        }

        $expected = hash_hmac('sha256', $data['action'] . $user_token . $data['time'], $this->secret_key);

        return hash_equals($expected, $data['nonce']);
    }
}
?>
