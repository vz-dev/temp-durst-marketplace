<?php

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant;

use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use stdClass;

class AbstractProductHydrator
{
    /**
     * @var AppRestApiClientInterface
     */
    protected $client;

    /**
     * @var AppRestApiConfig
     */
    protected $config;



    /**
     * @return stdClass
     */
    protected function createStdClass(): stdClass
    {
        return new stdClass();
    }

    /**
     * @param string|null $url
     * @param string $host
     * @return string
     */
    protected function formatImageUrl(?string $url, string $host): string
    {
        if ($url === null || $url === '') {
            return $this
                ->formatImageUrl(
                    $this->config->getFallbackImageProduct(),
                    $host
                );
        }

        return sprintf(
            '%s/%s',
            $host,
            $url
        );
    }

    /**
     * @param string|null $url
     * @return string
     */
    protected function formatBig(?string $url = null): string
    {
        return $this
            ->formatImageUrl(
                $url,
                $this
                    ->config
                    ->getBigImageHost()
            );
    }

    /**
     * @param string|null $url
     * @return string
     */
    protected function formatThumb(?string $url = null): string
    {
        return $this
            ->formatImageUrl(
                $url,
                $this
                    ->config
                    ->getThumbImageHost()
            );
    }

    /**
     * @param string|null $url
     * @return string|null
     */
    protected function formatProductThumb(?string $url=null): ?string
    {
        if($url === null) {
            return null;
        }

        return $this
            ->formatThumb($url);
    }
}
