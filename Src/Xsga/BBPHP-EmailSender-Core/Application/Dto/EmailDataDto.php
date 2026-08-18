<?php

declare(strict_types=1);

namespace Xsga\BBPHP\EmailSender\Core\Application\Dto;

final class EmailDataDto
{
    public string $sender = '';
    public string $senderName = '';

    /** @var string[] $recipients */
    public array $recipients = [];

    /** @var string[] $recipientsCC */
    public array $recipientsCC = [];

    /** @var string[] $recipientsBCC */
    public array $recipientsBCC = [];

    public string $subject = '';
    public string $body = '';

    /** @var string[] $attachments */
    public array $attachments = [];
}
