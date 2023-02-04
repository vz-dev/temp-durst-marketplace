<?php
/**
 * Durst - project - HttpRequestBusinessFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 11:41
 */

namespace Pyz\Zed\HttpRequest\Business;


use Pyz\Zed\HttpRequest\Business\Model\HttpRequest;
use Pyz\Zed\HttpRequest\Business\Model\HttpRequestInterface;
use Pyz\Zed\HttpRequest\Persistence\HttpRequestQueryContainerInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * Class HttpRequestBusinessFactory
 * @package Pyz\Zed\HttpRequest\Business
 * @method HttpRequestQueryContainerInterface getQueryContainer()
 */
class HttpRequestBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\HttpRequest\Business\Model\HttpRequestInterface
     */
    public function createHttpRequestModel(): HttpRequestInterface
    {
        return new HttpRequest(
            $this->getQueryContainer()
        );
    }
}
