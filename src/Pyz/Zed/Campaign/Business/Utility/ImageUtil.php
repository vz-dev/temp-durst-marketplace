<?php
/**
 * Durst - project - ImageUtil.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.06.21
 * Time: 09:50
 */

namespace Pyz\Zed\Campaign\Business\Utility;


use Pyz\Zed\Campaign\CampaignConfig;

class ImageUtil implements ImageUtilInterface
{
    /**
     * @var \Pyz\Zed\Campaign\CampaignConfig
     */
    protected $config;

    /**
     * ImageUtil constructor.
     * @param \Pyz\Zed\Campaign\CampaignConfig $config
     */
    public function __construct(
        CampaignConfig $config
    )
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param string|null $url
     * @param string $host
     * @return string
     */
    public function formatImageUrl(
        ?string $url,
        string $host
    ): string
    {
        if (
            $url === null ||
            $url === ''
        ) {
            return $this
                ->formatImageUrl(
                    $this
                        ->config
                        ->getFallbackImageProduct(),
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
     * {@inheritDoc}
     *
     * @param string|null $url
     * @return string
     */
    public function formatBig(?string $url = null): string
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
     * {@inheritDoc}
     *
     * @param string|null $url
     * @return string
     */
    public function formatThumb(?string $url = null): string
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
     * {@inheritDoc}
     *
     * @param string|null $url
     * @return string|null
     */
    public function formatProductThumb(?string $url = null): ?string
    {
        if ($url === null) {
            return null;
        }

        return $this
            ->formatThumb(
                $url
            );
    }
}
