<?php
/**
 * Durst - project - HttpRequestCommunicationFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 21.11.19
 * Time: 10:33
 */

namespace Pyz\Zed\HttpRequest\Communication;


use Pyz\Service\HttpRequest\Dependencies\External\Api\Client\HttpClientInterface;
use Pyz\Service\HttpRequest\Dependencies\External\Api\Client\HttpOptionInterface;
use Pyz\Service\HttpRequest\HttpRequestServiceInterface;
use Pyz\Zed\HttpRequest\Communication\Table\HttpRequestTable;
use Pyz\Zed\HttpRequest\HttpRequestDependencyProvider;
use Pyz\Zed\HttpRequest\Persistence\HttpRequestQueryContainerInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

/**
 * Class HttpRequestCommunicationFactory
 * @package Pyz\Zed\HttpRequest\Communication
 * @method HttpRequestQueryContainerInterface getQueryContainer()
 */
class HttpRequestCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Pyz\Service\HttpRequest\HttpRequestServiceInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getHttpRequestService(): HttpRequestServiceInterface
    {
        return $this
            ->getProvidedDependency(HttpRequestDependencyProvider::SERVICE_HTTP_REQUEST);
    }

    /**
     * @param \Pyz\Service\HttpRequest\Dependencies\External\Api\Client\HttpOptionInterface|null $options
     * @return \Pyz\Service\HttpRequest\Dependencies\External\Api\Client\HttpClientInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getHttpClient(?HttpOptionInterface $options): HttpClientInterface
    {
        return $this
            ->getHttpRequestService()
            ->getHttpClient($options);
    }

    /**
     * @return \Pyz\Service\HttpRequest\Dependencies\External\Api\Client\HttpOptionInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getHttpOptions(): HttpOptionInterface
    {
        return $this
            ->getHttpRequestService()
            ->getHttpOptions();
    }

    /**
     * @return \Pyz\Zed\HttpRequest\Communication\Table\HttpRequestTable
     */
    public function createHttpRequestTable(): HttpRequestTable
    {
        return new HttpRequestTable(
            $this->getQueryContainer()
        );
    }
}
