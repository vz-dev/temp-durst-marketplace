<?php

namespace Pyz\Zed\Sentry;

use Pyz\Shared\Sentry\SentryConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class SentryConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getSentryDsn(): string
    {
        return $this->get(SentryConstants::SENTRY_DSN);
    }
}
