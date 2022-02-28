Email two-factor authentication for laravel-admin
======
Adds two-factor authentication to admin login, admin will be sent an email with a 6-digit code to complete sign in.

### Notes
A new "email" field is added to the database as laravel-admin does not have this by default. Once installed and migrations completed, you will need to set each user's email address via the database. No way of doing it via UI has been implemented at this time.

### Installation

```
composer require shanerutter/laravel-admin-email-two-factor
```

### Migration
Add email address field to admin users table.
```
php artisan migrate
```

### Configuration

In the extensions section of the `config/admin.php` file, add configurations
```
'extensions' => [
    'auth-email-two-factor' => [
        'enable' => (bool)env('ADMIN_AUTH_EMAIL_TWO_FACTOR', true),
        'pinLength' => (int)env('ADMIN_AUTH_EMAIL_TWO_FACTOR_PIN_LENGTH', 6),
        'rememberDays' => (bool)env('ADMIN_AUTH_EMAIL_TWO_FACTOR_REMEMBER_DAYS', 1),
    ]
]
```

In the `.env` file, add configurations
```
ADMIN_AUTH_EMAIL_TWO_FACTOR=true
ADMIN_AUTH_EMAIL_TWO_FACTOR_PIN_LENGTH=6
ADMIN_AUTH_EMAIL_TWO_FACTOR_REMEMBER_DAYS=1
```

### License

Licensed under [The MIT License (MIT)](LICENSE).

