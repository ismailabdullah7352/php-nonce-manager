<?php
class NonceManager {
    private $nonce_lifetime = 3600; // صلاحية nonce بالثواني (1 ساعة)

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialize nonces array if not exists
        if (!isset($_SESSION['_nonces'])) {
            $_SESSION['_nonces'] = [];
        }
        
        // Clean up expired nonces
        $this->cleanup_expired_nonces();
    }

    public function create_nonce($action) {
        if (empty($action)) {
            throw new InvalidArgumentException('Action must not be empty');
        }
        
        $uid = session_id();
        $tick = $this->nonce_tick();
        $salt = bin2hex(random_bytes(16)); // Add random salt for more security
        $token = hash('sha256', $salt . '|' . $uid . '|' . $action . '|' . $tick);
        
        $_SESSION['_nonces'][$action] = [
            'token' => $token,
            'created' => time(),
            'salt' => $salt
        ];
        
        return $token;
    }

    public function verify_nonce($nonce, $action) {
        if (empty($nonce) || empty($action)) {
            return false;
        }
        
        if (!isset($_SESSION['_nonces'][$action])) {
            return false;
        }

        $stored = $_SESSION['_nonces'][$action];
        
        // Verify expiration
        if (time() - $stored['created'] > $this->nonce_lifetime) {
            unset($_SESSION['_nonces'][$action]);
            return false;
        }

        // Verify token
        $expected = $stored['token'];
        if (!hash_equals($expected, $nonce)) {
            return false;
        }
        
        // Invalidate after single use (optional)
        unset($_SESSION['_nonces'][$action]);
        
        return true;
    }

    public function nonce_field($action, $name = '_nonce', $echo = true) {
        $nonce = $this->create_nonce($action);
        $field = '<input type="hidden" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . 
                 '" value="' . htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8') . '">';
        
        if ($echo) {
            echo $field;
        }
        
        return $field;
    }

    public function nonce_url($url, $action, $name = '_nonce') {
        $nonce = $this->create_nonce($action);
        $separator = (parse_url($url, PHP_URL_QUERY) === null) ? '?' : '&';
        return $url . $separator . urlencode($name) . '=' . urlencode($nonce);
    }

    private function nonce_tick() {
        return ceil(time() / ($this->nonce_lifetime / 2));
    }
    
    private function cleanup_expired_nonces() {
        foreach ($_SESSION['_nonces'] as $action => $nonce_data) {
            if (time() - $nonce_data['created'] > $this->nonce_lifetime) {
                unset($_SESSION['_nonces'][$action]);
            }
        }
    }
}
