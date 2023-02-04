<?php

namespace Pyz\Zed\Mail;

use Pyz\Shared\Mail\MailConstants;
use Spryker\Zed\Mail\MailConfig as SprykerMailConfig;

class MailConfig extends SprykerMailConfig
{
    const DEFAULT_SMTP_HOST = 'localhost';
    const DEFAULT_SMTP_PORT = '25';

    /**
     * @return string
     */
    public function getSmtpHost() : string
    {
        return $this->get(MailConstants::MAIL_SMTP_HOST, self::DEFAULT_SMTP_HOST);
    }

    /**
     * @return int
     */
    public function getSmtpPort(): int
    {
        return $this->get(MailConstants::MAIL_SMTP_PORT, self::DEFAULT_SMTP_PORT);
    }
}
