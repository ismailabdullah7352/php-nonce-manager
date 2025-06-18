<?php
require_once 'NonceManager.php';

// Initialize the Nonce Manager
$nonceManager = new NonceManager();

// Start the session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Nonce Manager Example</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .container { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        h2 { color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 3px; overflow-x: auto; }
        .success { color: #27ae60; }
        .error { color: #e74c3c; }
    </style>
</head>
<body>
    <h1>PHP Nonce Manager Example</h1>
    
    <div class="container">
        <h2>1. Basic Form Protection</h2>
        
        <?php
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['basic_form'])) {
            if ($nonceManager->verify_nonce($_POST['_nonce'], 'basic_form_action')) {
                $message = '<p class="success">Form submitted successfully! Nonce verified.</p>';
            } else {
                $message = '<p class="error">Error: Invalid nonce! Possible CSRF attempt.</p>';
            }
            echo $message;
        }
        ?>
        
        <form method="post">
            <input type="hidden" name="basic_form" value="1">
            <p>This form demonstrates basic CSRF protection:</p>
            <label>Test Input: <input type="text" name="test_input"></label>
            
            <?php
            // Add nonce field (automatically echoes)
            $nonceManager->nonce_field('basic_form_action');
            ?>
            
            <button type="submit">Submit Protected Form</button>
        </form>
    </div>
    
    <div class="container">
        <h2>2. URL Token Protection</h2>
        
        <?php
        // Process URL token
        if (isset($_GET['action']) && $_GET['action'] === 'protected') {
            if ($nonceManager->verify_nonce($_GET['_nonce'], 'url_action')) {
                echo '<p class="success">URL action completed successfully! Nonce verified.</p>';
            } else {
                echo '<p class="error">Error: Invalid nonce in URL! Possible CSRF attempt.</p>';
            }
        }
        ?>
        
        <p>This demonstrates how to protect GET requests:</p>
        
        <?php
        // Generate protected URL
        $protectedUrl = $nonceManager->nonce_url('example.php?action=protected', 'url_action');
        ?>
        
        <a href="<?= htmlspecialchars($protectedUrl) ?>">Click this protected link</a>
        
        <pre>Generated URL: <?= htmlspecialchars($protectedUrl) ?></pre>
    </div>
    
    <div class="container">
        <h2>3. Custom Nonce Field Name</h2>
        
        <?php
        // Process custom nonce field
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_form'])) {
            if ($nonceManager->verify_nonce($_POST['custom_token'], 'custom_action')) {
                $message = '<p class="success">Custom nonce field verified successfully!</p>';
            } else {
                $message = '<p class="error">Error: Custom nonce validation failed!</p>';
            }
            echo $message;
        }
        ?>
        
        <form method="post">
            <input type="hidden" name="custom_form" value="1">
            <p>This form uses a custom nonce field name:</p>
            
            <?php
            // Add nonce field with custom name (doesn't auto-echo)
            $customNonceField = $nonceManager->nonce_field('custom_action', 'custom_token', false);
            echo $customNonceField;
            ?>
            
            <button type="submit">Submit with Custom Nonce</button>
        </form>
    </div>
    
    <div class="container">
        <h2>4. Manual Nonce Creation/Verification</h2>
        
        <?php
        // Manual nonce example
        if (isset($_POST['manual_verify'])) {
            $receivedNonce = $_POST['manual_nonce'] ?? '';
            if ($nonceManager->verify_nonce($receivedNonce, 'manual_action')) {
                echo '<p class="success">Manually verified nonce successfully!</p>';
            } else {
                echo '<p class="error">Manual nonce verification failed!</p>';
            }
        }
        
        // Create a nonce manually
        $manualNonce = $nonceManager->create_nonce('manual_action');
        ?>
        
        <p>This demonstrates manual nonce handling (useful for AJAX requests):</p>
        
        <pre>Generated Nonce: <?= htmlspecialchars($manualNonce) ?></pre>
        
        <form method="post">
            <input type="hidden" name="manual_nonce" value="<?= htmlspecialchars($manualNonce) ?>">
            <input type="hidden" name="manual_verify" value="1">
            <button type="submit">Verify Manual Nonce</button>
        </form>
    </div>
    
    <div class="container">
        <h2>5. Nonce Expiration Test</h2>
        
        <?php
        // Create a nonce with short lifetime
        class ShortLivedNonceManager extends NonceManager {
            protected $nonce_lifetime = 5; // 5 seconds for testing
        }
        
        $shortNonceManager = new ShortLivedNonceManager();
        $expiringNonce = $shortNonceManager->create_nonce('expiring_action');
        
        if (isset($_POST['test_expiration'])) {
            if ($shortNonceManager->verify_nonce($_POST['expiring_nonce'], 'expiring_action')) {
                echo '<p class="success">Nonce is still valid!</p>';
            } else {
                echo '<p class="error">Nonce has expired! (lifetime: 5 seconds)</p>';
            }
        }
        ?>
        
        <p>This demonstrates nonce expiration (5 second lifetime):</p>
        
        <pre>Nonce will expire at: <?= date('H:i:s', time() + 5) ?></pre>
        <pre>Current time: <?= date('H:i:s') ?></pre>
        
        <form method="post">
            <input type="hidden" name="expiring_nonce" value="<?= htmlspecialchars($expiringNonce) ?>">
            <input type="hidden" name="test_expiration" value="1">
            <button type="submit">Test Expiration</button>
        </form>
        
        <p>Try submitting this form after 5 seconds to see the expiration in action.</p>
    </div>
</body>
</html>
