# BBPHP-EmailSender-Core

Email sending core for PHP applications, built on an abstraction layer and DTOs to decouple the domain from the concrete delivery infrastructure.

## Description

This package defines the minimal interface and models needed to send emails from any PHP application without coupling the business logic directly to concrete providers such as SMTP, Resend, or other external services.

The library is designed as a reusable core for projects that need to:

- decouple business logic from email infrastructure;
- normalize the structure of email content;
- allow the delivery provider to change without rewriting the business flow;
- maintain a clear and typed contract for messages.

## Project status

This repository contains the base layer of the system:

- supported email method enum;
- email service interface;
- DTO for transferring email information;
- domain-specific exception.

It does not include a final SMTP or Resend implementation in this package; that level is handled in consumer-layer adapters.

## Requirements

- PHP 8.4+
- Composer
- Main dependency: `xsga/bbphp-exception`

## Installation

```bash
composer require xsga/bbphp-emailsender-core
```

## Package structure

```text
Src/
└── Xsga/
    └── BBPHP-EmailSender-Core/
        ├── Application/
        │   ├── Dto/
        │   │   └── EmailDataDto.php
        │   └── Services/
        │       └── SendEmailService.php
        └── Domain/
            ├── EmailMethods.php
            └── Exceptions/
                └── SendEmailException.php
```

## Main components

### `EmailMethods`

Enum that defines the supported providers for the core:

```php
namespace Xsga\BBPHP\EmailSender\Core\Domain;

enum EmailMethods: string
{
    case SMTP = 'smtp';
    case RESEND = 'resend';
}
```

### `SendEmailService`

Interface that defines the contract for any concrete implementation:

```php
namespace Xsga\BBPHP\EmailSender\Core\Application\Services;

use Xsga\BBPHP\EmailSender\Core\Application\Dto\EmailDataDto;

interface SendEmailService
{
    public function send(EmailDataDto $emailData): void;
}
```

### `EmailDataDto`

DTO containing the minimum information needed to build a message:

```php
final class EmailDataDto
{
    public string $sender = '';
    public string $senderName = '';
    public array $recipients = [];
    public array $recipientsCC = [];
    public array $recipientsBCC = [];
    public string $subject = '';
    public string $body = '';
    public array $attachments = [];
}
```

### `SendEmailException`

Domain exception for sending failures.

## Usage example

```php
<?php

use Xsga\BBPHP\EmailSender\Core\Application\Dto\EmailDataDto;
use Xsga\BBPHP\EmailSender\Core\Application\Services\SendEmailService;

/** @var SendEmailService $emailService */
$emailData = new EmailDataDto();
$emailData->sender = 'noreply@my-domain.com';
$emailData->senderName = 'Support';
$emailData->recipients = ['customer@example.com'];
$emailData->subject = 'Welcome';
$emailData->body = '<h1>Hello</h1><p>Thank you for registering.</p>';

$emailService->send($emailData);
```

## Design principle

The package follows a contract-based abstraction approach:

- `EmailDataDto` represents the message to be delivered.
- `SendEmailService` represents the sending mechanism.
- `EmailMethods` identifies the provider type.
- the actual implementation is delegated to the infrastructure layer of the consuming project.

This allows the email technology to be substituted without affecting the main application logic.

## License

This project is licensed under the MIT license.

## Author

Parker

## Useful links

- Repository: `https://github.com/xsga/BBPHP-EmailSender-Core`
- Composer package: `xsga/bbphp-emailsender-core`
