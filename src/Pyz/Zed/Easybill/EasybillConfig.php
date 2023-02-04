<?php

namespace Pyz\Zed\Easybill;

use Pyz\Shared\Easybill\EasybillConfig as Config;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class EasybillConfig extends AbstractBundleConfig
{
    protected const DEFAULT_INVOICE_DELAY_QUEUE_CHUNK_SIZE = 10;

    /**
     * @return string
     */
    public function getEasybillApiUrl(string $resource): string
    {
        return sprintf(
            '%s%s',
            $this->get(Config::EASYBILL_API_URL),
            $resource
        );
    }

    /**
     * @return string
     */
    public function getEasybillEmail(): string
    {
        return $this
            ->get(Config::EASYBILL_EMAIL);
    }

    /**
     * @return string
     */
    public function getEasybillApiKey(): string
    {
        return $this
            ->get(Config::EASYBILL_API_KEY);
    }

    /**
     * @return int
     */
    public function getInvoiceDelayQueueChunkSize(): int
    {
        return $this
            ->get(
                Config::INVOICE_DELAY_QUEUE_CHUNK_SIZE,
                self::DEFAULT_INVOICE_DELAY_QUEUE_CHUNK_SIZE
            );
    }
}
