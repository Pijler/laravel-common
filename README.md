# 📌 Laravel Common

A Laravel package that contains common functionalities I use in almost all projects I develop. This package includes traits, helpers, macros, commands, and other utilities that speed up development.

### 📦 Installation

You can install the package via Composer:

```bash
composer require pijler/laravel-common
```

The package will be automatically discovered by Laravel.

### 🧩 Features

#### 🎯 Actions

Abstract base class for executing actions in a clean and organized way:

```php
use Common\Support\Action;

class CreateUserAction extends Action
{
    public function __construct(
        private string $name,
        private string $email
    ) {}

    protected function handle()
    {
        return User::create([
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }
}

// Usage
$user = CreateUserAction::execute(
    name: 'João Pedro',
    email: 'joao@example.com',
);

// With conditions
CreateUserAction::executeIf($shouldCreate, 'João Pedro', 'joao@example.com');
CreateUserAction::executeUnless($shouldNotCreate, 'João Pedro', 'joao@example.com');
```

#### 🔐 Two-Factor Authentication

Trait for implementing two-factor authentication:

```php
use Common\Traits\HasTwoFactor;

class User extends Model
{
    use HasTwoFactor;
}

// Check if user has 2FA enabled
$user->hasTwoFactor();

// Get recovery codes
$codes = $user->recoveryCodes();

// Replace recovery code
$user->replaceRecoveryCode($oldCode);

// Get QR Code SVG
$qrCode = $user->twoFactorQrCodeSvg();

// Get QR Code URL
$url = $user->twoFactorQrCodeUrl();
```

#### 📱 User Agent Detection

Class for detecting browser and device information:

```php
use Common\Support\Agent;

$agent = new Agent();

// Device information
$agent->isMobile();
$agent->isTablet();
$agent->isDesktop();

// Browser information
$agent->browser(); // Chrome, Firefox, Safari, etc.

// Operating system information
$agent->platform(); // Windows, macOS, Linux, etc.
```

#### 🚨 Alert System

Alert system with typed exceptions:

```php
use Common\Enum\Alert;
use Common\Exceptions\Alert\InfoException;
use Common\Exceptions\Alert\ErrorException;
use Common\Exceptions\Alert\WarningException;

// Throw alert exceptions
InfoException::make('Info Message!');
ErrorException::make('Error Message!');
WarningException::make('Warning Message!');

// Helpers to check exceptions
alert_check_exception($exception); // bool
alert_throw_exception($exception); // void
```

#### 📨 Storage Channel

Notification channel that saves emails to files and database:

```php
use Common\Channel\StorageChannel;

// Configure callback for custom path
StorageChannel::storagePathUsing(function ($notification) {
    return "/custom/path/{$notification->id}.html";
});

// Use in notifications
class WelcomeNotification extends Notification
{
    public function via($notifiable)
    {
        return ['storage'];
    }
}
```

#### 🛠️ Macros

Useful macros for Eloquent, RedirectResponse and TestResponse:

##### Eloquent Builder

```php
// Get first random record
User::firstRandom();
```

##### RedirectResponse

```php
// Alert messages
return redirect()->info('Info Message!');
return redirect()->error('Error Message!');
return redirect()->success('Success Message!');
return redirect()->warning('Warning Message!');

// Custom message
return redirect()->message('Message text', Alert::INFO);

// Custom action
return redirect()->action(ActionData::from([
    'text' => 'Undo',
    'method' => 'patch',
    'url' => "/users/{$user->id}/restore",
]));
```

##### TestResponse

```php
// Message assertions
$response->assertInfoMessage('Info Message!');
$response->assertErrorMessage('Error Message!');
$response->assertSuccessMessage('Success Message!');
$response->assertWarningMessage('Warning Message!');

// Action assertion
$response->assertAction(ActionData::from([
    'text' => 'Undo',
    'method' => 'patch',
    'url' => "/users/{$user->id}/restore",
]));
```

##### Inertia.js (if available)

```php
// Automatic filters
return Inertia::render('Users/Index')->filters([
    'role' => 'admin',
    'status' => 'active',
]);

// Pagination parameters
return Inertia::render('Users/Index')->params([
    'page' => 1,
    'limit' => 10,
    'sort' => 'name',
]);
```

#### 🗄️ Database Utilities

##### Rename Migrations Command

```bash
php artisan migrate:rename
```

This command renames migration files to follow a consistent pattern.

#### 🔒 File Encryption Commands

Commands for encrypting and decrypting files:

##### Encrypt File Command

```bash
php artisan file:encrypt --filename=.npmrc
```

**Options:**

- `--key`: The encryption key (if not provided, a random key will be generated)
- `--cipher`: The encryption cipher (default: `AES-256-CBC`)
- `--path`: Path to write the encrypted file (default: `base_path()`)
- `--filename`: Filename of the file to encrypt (required)
- `--prune`: Delete the original file after encryption
- `--force`: Overwrite the existing encrypted file

**Interactive Mode:**
If run interactively without options, the command will prompt for:

- Filename to encrypt
- Encryption key (with option to generate a random key or provide your own)

**Examples:**

```bash
# Encrypt a file with automatic key generation
php artisan file:encrypt --filename=.npmrc

# Encrypt with a specific key
php artisan file:encrypt --filename=.npmrc --key="your-encryption-key"

# Encrypt and delete original file
php artisan file:encrypt --filename=.npmrc --prune

# Encrypt with custom cipher
php artisan file:encrypt --filename=.npmrc --cipher=AES-128-CBC

# Encrypt and force overwrite existing encrypted file
php artisan file:encrypt --filename=.npmrc --force
```

The encrypted file will be saved with `.encrypted` extension (e.g., `.npmrc.encrypted`).

##### Decrypt File Command

```bash
php artisan file:decrypt --filename=.npmrc.encrypted
```

**Options:**

- `--key`: The decryption key (if not provided, will use `LARAVEL_ENV_ENCRYPTION_KEY` from environment)
- `--cipher`: The encryption cipher (default: `AES-256-CBC`)
- `--path`: Path to write the decrypted file (default: `base_path()`)
- `--filename`: Filename of the encrypted file to decrypt (required, must end with `.encrypted`)
- `--force`: Overwrite the existing decrypted file

**Interactive Mode:**
If run interactively without options, the command will prompt for:

- Filename to decrypt
- Decryption key (if not available in environment)

**Examples:**

```bash
# Decrypt a file (uses LARAVEL_ENV_ENCRYPTION_KEY from .env)
php artisan file:decrypt --filename=.npmrc.encrypted

# Decrypt with a specific key
php artisan file:decrypt --filename=.npmrc.encrypted --key="your-encryption-key"

# Decrypt with base64 encoded key
php artisan file:decrypt --filename=.npmrc.encrypted --key="base64:encoded-key"

# Decrypt and force overwrite existing file
php artisan file:decrypt --filename=.npmrc.encrypted --force
```

The decrypted file will be saved without the `.encrypted` extension.

#### 🎨 Enum Helpers

Trait for enums with useful methods:

```php
use Common\Traits\EnumMethods;

enum Status: string
{
    use EnumMethods;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}

// Available methods
Status::keys(); // ['ACTIVE', 'INACTIVE']
Status::values(); // ['active', 'inactive']
```

#### 📁 Media Library Extensions

Extensions for Spatie Media Library:

- **CustomFileNamer**: Custom file naming
- **CustomPathGenerator**: Custom path generation

#### 🔗 Notification URL

Trait for generating notification URLs:

```php
use Common\Traits\NotificationUrl;

class User extends Model
{
    use NotificationUrl;
}

// Generate URL for notification
$url = $user->notificationUrl($notification);
```

#### 🏗️ Builder Helpers

Trait for adding useful methods to Eloquent Builders:

```php
use Common\Traits\HasBuilder;

class User extends Model
{
    use HasBuilder;
}

// Methods available automatically on builders
User::query()->whereActive();
User::query()->whereInactive();
```

#### ⚡ Horizon Queue

Trait for working with Laravel Horizon:

```php
use Common\Traits\HorizonQueue;

class ProcessDataJob implements ShouldQueue
{
    use HorizonQueue;
}
```

### 📝 License

Open-source under the [MIT license](LICENSE).

## 🚀 Thanks!

_This package contains common functionalities I use in my Laravel projects. Feel free to use and contribute!_
