<?php
/**
 * Durst - project - HttpRequestServiceFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 11:34
 */

namespace Pyz\Service\HttpRequest;


use Pyz\Service\HttpRequest\Dependencies\External\Api\Client\HttpClient;
use Pyz\Service\HttpRequest\Dependencies\External\Api\Client\HttpClientInterface;
use Spryker\Service\Kernel\AbstractServiceFactory;

class HttpRequestServiceFactory extends AbstractServiceFactory
{
    /**
     * @return \Pyz\Service\HttpRequest\Dependencies\External\Api\Client\HttpClientInterface
     */
    public function createHttpClient(): HttpClientInterface
    {
        return new HttpClient();
    }
}
