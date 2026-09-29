# Technical documentation · BBPHP Email Sender Core

## 1. Objective

This repository defines the abstraction core for sending emails in PHP applications. Its goal is to provide a common contract for constructing and sending messages without depending on a concrete provider implementation.

In other words, the package does not send emails by itself; it provides a base layer on which to build adapters or specific implementations for SMTP, REST APIs, commercial providers, or internal services.

## 2. Project context

- Package name: `xsga/bbphp-emailsender-core`
- Type: reusable PHP library
- Minimum PHP version: 8.4
- Base namespace: `Xsga\BBPHP\EmailSender\Core\`
- Main dependency: `xsga/bbphp-exception`

## 3. Scope

The package covers the domain and application layers of the email functionality:

- email method enum;
- email service interface;
- DTO for email content;
- domain exception for send failures.

The concrete infrastructure layer (for example SMTP, Resend, Mailgun, SES, etc.) is outside this repository and should be implemented in a consumer project adapter.

## 4. Architecture

The package design is based on a clear separation between domain and application layers:

### 4.1 Domain layer

Responsible for the core concepts of the email domain.

- `EmailMethods`: enum representing the supported sending methods.
- `SendEmailException`: domain-specific exception for sending failures.

### 4.2 Application layer

Responsible for contracts and transfer objects used by the application.

- `SendEmailService`: interface with the `send()` method.
- `EmailDataDto`: container with the data of the email to send.

### 4.3 Pattern used

The project uses a strategy-pattern variant:

- business logic consumes an abstraction: `SendEmailService`;
- each concrete implementation decides the real sending mechanism;
- the consumer only needs to know the interface and the structure of `EmailDataDto`.

This allows changing providers without introducing tight coupling in the business logic.

## 5. Repository structure

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

## 6. Main components

### 6.1 `EmailMethods`

File: `Src/Xsga/BBPHP-EmailSender-Core/Domain/EmailMethods.php`

```php
namespace Xsga\BBPHP\EmailSender\Core\Domain;

enum EmailMethods: string
{
    case SMTP = 'smtp';
    case RESEND = 'resend';
}
```

#### Purpose

Represents the available sending channel or method. In its current state it defines only two values:

- `SMTP`
- `RESEND`

This enum allows the consuming project to choose or validate the delivery provider at configuration or dependency injection time.

### 6.2 `SendEmailService`

File: `Src/Xsga/BBPHP-EmailSender-Core/Application/Services/SendEmailService.php`

```php
namespace Xsga\BBPHP\EmailSender\Core\Application\Services;

use Xsga\BBPHP\EmailSender\Core\Application\Dto\EmailDataDto;

interface SendEmailService
{
    public function send(EmailDataDto $emailData): void;
}
```

#### Purpose

Defines the minimum contract for all email sending adapters. Every implementation must accept an `EmailDataDto` and should not return a response; it should throw an exception if sending fails.

### 6.3 `EmailDataDto`

File: `Src/Xsga/BBPHP-EmailSender-Core/Application/Dto/EmailDataDto.php`

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

#### Purpose

Groups all data needed to build an email. Its design allows:

- defining the sender and display name;
- defining primary recipients and copies;
- including a subject and HTML body;
- preparing a list of attachments for future implementations.

### 6.4 `SendEmailException`

File: `Src/Xsga/BBPHP-EmailSender-Core/Domain/Exceptions/SendEmailException.php`

```php
final class SendEmailException extends GenericException
```

#### Purpose

Centralizes the exception for sending errors. By extending `GenericException`, it remains compatible with the base exception library used by the project.

## 7. Usage flow

The typical process for this package is:

1. the consumer creates an `EmailDataDto`;
2. the consumer selects or configures the appropriate `SendEmailService`;
3. `send($emailData)` is invoked;
4. if the provider fails, a `SendEmailException` is thrown.

```mermaid
flowchart LR
    A[Application / use case] --> B[EmailDataDto]
    B --> C[SendEmailService]
    C --> D[Concrete implementation]
    D --> E[SMTP or API provider]
```

## 8. Integration example

```php
<?php

use Xsga\BBPHP\EmailSender\Core\Application\Dto\EmailDataDto;
use Xsga\BBPHP\EmailSender\Core\Application\Services\SendEmailService;

/** @var SendEmailService $emailService */

$emailData = new EmailDataDto();
$emailData->sender = 'noreply@mydomain.com';
$emailData->senderName = 'My application';
$emailData->recipients = ['user@example.com'];
$emailData->recipientsCC = ['support@example.com'];
$emailData->subject = 'Registration confirmation';
$emailData->body = '<h1>Hello</h1><p>Your account has been created successfully.</p>';

$emailService->send($emailData);
```

## 9. Design considerations

### 9.1 Separation of responsibilities

The package does not mix:

- business rules from the application;
- message personalization logic;
- external provider configuration;
- real delivery infrastructure.

This helps keep the code more maintainable and extensible.

### 9.2 Provider flexibility

The `EmailMethods` enum makes it easier to decide which implementation to use. A dependency container, factory, or environment-based configuration can resolve the appropriate service according to the desired method.

### 9.3 Extensibility

The repository is ready to grow with new implementations, for example:

- `SmtpEmailService`
- `ResendEmailService`
- `SesEmailService`
- `MailgunEmailService`

Each of these classes only needs to implement `SendEmailService` and accept the same DTO.

## 10. Current limitations

The current package is a minimal core and has several intentional limitations:

- it has no concrete sending implementation;
- it does not validate email data or recipient format;
- it does not manage queues or retries;
- it does not include templates or content rendering;
- it has no SMTP or Resend adapter in this repository.

These choices are consistent with its nature as a contract and domain library rather than a finished infrastructure package.

## 11. Recommendations for use

- keep the email content prepared before invoking `send()`;
- validate senders and recipients in the application or infrastructure layer;
- handle `SendEmailException` at the integration point;
- use the same DTO across providers to avoid migration changes;
- centralize provider configuration in the infrastructure layer of the consuming project.

## 12. Security

Although this package does not open network connections or send messages directly, it is still important to consider:

- not logging sensitive email content;
- not storing provider credentials in source code;
- sanitizing HTML if dynamic content is included;
- validating the origin of recipients and senders before sending.

## 13. Conclusion

`BBPHP-EmailSender-Core` is a foundational package for the application email layer. Its main value is defining a stable contract for message delivery and leaving the real implementation to specific adapters.

With this approach, the application can evolve between email providers without affecting the business logic that generates the message.
