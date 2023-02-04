<?php
/**
 * Durst - project - HttpRequestConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 11:58
 */

namespace Pyz\Zed\HttpRequest;


use Pyz\Shared\HttpRequest\HttpRequestConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class HttpRequestConfig extends AbstractBundleConfig
{
    /**
     * @return int
     */
    public function getHttpRequestTimeout(): int
    {
        return $this
            ->get(HttpRequestConstants::HTTP_REQUEST_TIMEOUT);
    }

    /**
     * @return int
     */
    public function getHttpRequestConnectionTimeout(): int
    {
        return $this
            ->get(HttpRequestConstants::HTTP_CONNECT_TIMEOUT);
    }
}
