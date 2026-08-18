<?php

declare(strict_types=1);

namespace Xsga\BBPHP\EmailSender\Core\Domain;

enum EmailMethods: string
{
    case SMTP = 'smtp';
    case RESEND = 'resend';
}
