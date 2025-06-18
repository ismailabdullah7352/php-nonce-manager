# PHP Nonce Manager

A lightweight PHP class to generate and verify one-time tokens (nonces) for secure form submissions, inspired by WordPress-style tokens.

## 🔐 Features

- Stateless secure token with HMAC
- Includes timestamp to prevent replay attacks
- Token tied to session (user-specific)
- Adjustable lifetime (default: 30 minutes)
- Easy integration for any PHP project

## 📦 Files

```
NonceManager.php       → Main class
example-usage.php      → How to use with POST forms
```

## 🛠 How to Use

### 1. Create a nonce token:

```php
$nonce = new NonceManager();
$token = $nonce->create_nonce('my_action');
```

### 2. Embed in a form:

```html
<input type="hidden" name="_nonce" value="<?= $token ?>">
```

### 3. Verify token on POST:

```php
if ($nonce->verify_nonce($_POST['_nonce'], 'my_action')) {
    // Success
} else {
    // Invalid
}
```

## ⚙️ Configuration

Edit the following inside `NonceManager.php`:

```php
private $secret_key = 'change_this_secret';    // Your own app secret
private $nonce_lifetime = 1800;                // Lifetime in seconds
```

## 📁 Example Output

The nonce is a base64-encoded JSON string containing:

```json
{
  "nonce": "a1b2c3...",
  "time": 1718643901,
  "action": "my_action"
}
```

## ✅ Security Notes

- Ensure session is started
- Keep `secret_key` private and unique per app
- Avoid storing nonces in DB — this approach is stateless

---

MIT License. Developed with ❤️ for secure PHP development.
