<?php

declare(strict_types=1);

namespace Xsga\BBPHP\EmailSender\Core\Application\Services;

use Xsga\BBPHP\EmailSender\Core\Application\Dto\EmailDataDto;

interface SendEmailService
{
    public function send(EmailDataDto $emailData): void;
}
