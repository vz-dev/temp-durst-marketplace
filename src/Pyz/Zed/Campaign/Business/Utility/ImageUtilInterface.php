<?php
/**
 * Durst - project - ImageUtilInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.06.21
 * Time: 09:50
 */

namespace Pyz\Zed\Campaign\Business\Utility;


interface ImageUtilInterface
{
    /**
     * @param string|null $url
     * @param string $host
     * @return string
     */
    public function formatImageUrl(
        ?string $url,
        string $host
    ): string;

    /**
     * @param string|null $url
     * @return string
     */
    public function formatBig(
        ?string $url = null
    ): string;

    /**
     * @param string|null $url
     * @return string
     */
    public function formatThumb(
        ?string $url = null
    ): string;

    /**
     * @param string|null $url
     * @return string|null
     */
    public function formatProductThumb(
        ?string $url = null
    ): ?string;
}
