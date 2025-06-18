
# PHP Nonce Manager 🛡️

A secure and lightweight PHP class for generating and validating nonces (CSRF tokens) to protect your web applications against Cross-Site Request Forgery attacks.

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

## Features

- 🚀 Simple and easy-to-use implementation
- 🔒 Secure nonce generation using SHA-256 hashing
- ⏳ Configurable lifetime for nonces (default: 1 hour)
- 💡 Supports both form fields and URL tokens
- 🛡️ Protection against timing attacks with `hash_equals()`
- 🧹 Automatic cleanup of expired nonces
- 🔄 Single-use tokens (optional)

## Installation

### Via Composer

```bash
composer require your-github-username/php-nonce-manager
```

### Manual Installation

1. Download the `NonceManager.php` file
2. Include it in your project:

```php
require_once 'path/to/NonceManager.php';
```

## Basic Usage

### Initialize the Nonce Manager

```php
$nonceManager = new NonceManager();
```

### Protecting Forms

1. Add a nonce field to your form:

```php
<form method="post" action="process.php">
    <!-- Your form fields here -->
    <?php $nonceManager->nonce_field('form_action'); ?>
    <button type="submit">Submit</button>
</form>
```

2. Verify the nonce when processing:

```php
if (!$nonceManager->verify_nonce($_POST['_nonce'], 'form_action')) {
    die('Invalid request: CSRF token validation failed');
}
```

### Protecting URLs

1. Generate a protected URL:

```php
$protectedUrl = $nonceManager->nonce_url('delete.php?id=123', 'delete_action');
echo '<a href="'.$protectedUrl.'">Delete Item</a>';
```

2. Verify the nonce when handling the request:

```php
if (!$nonceManager->verify_nonce($_GET['_nonce'], 'delete_action')) {
    die('Invalid request: CSRF token validation failed');
}
```

## Advanced Usage

### Custom Nonce Field Name

```php
// In form:
$nonceManager->nonce_field('update_profile', 'custom_nonce_name');

// When verifying:
$nonceManager->verify_nonce($_POST['custom_nonce_name'], 'update_profile');
```

### Custom Nonce Lifetime

Extend the class to modify the lifetime:

```php
class CustomNonceManager extends NonceManager {
    protected $nonce_lifetime = 1800; // 30 minutes
}
```

### Manual Nonce Creation/Verification

```php
// Create a nonce without output
$nonce = $nonceManager->create_nonce('api_request');

// Verify manually
if ($nonceManager->verify_nonce($received_nonce, 'api_request')) {
    // Valid request
}
```

## Security Best Practices

1. Always verify nonces before processing sensitive actions
2. Use different action names for different forms/actions
3. Combine with other security measures like input validation
4. Use HTTPS to prevent token interception
5. Consider making nonces single-use for critical actions

## Requirements

- PHP 7.4 or higher
- Sessions must be enabled

## Contributing

Contributions are welcome! Please open an issue or submit a pull request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a pull request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

If you find this project useful, please consider ⭐ starring it on GitHub!
```

This README includes:

1. Clear title and badges
2. Feature highlights
3. Installation instructions
4. Basic and advanced usage examples
5. Security best practices
6. Requirements
7. Contribution guidelines
8. License information
9. Support request

You can customize it further by:
- Adding screenshots if applicable
- Including a changelog section
- Adding a "Tests" section if you have tests
- Adding a "Credits" section if needed

Would you like me to add or modify any sections?
