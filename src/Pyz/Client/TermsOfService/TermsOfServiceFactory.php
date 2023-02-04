<?php
/**
 * Durst - project - TermsOfServiceFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.05.18
 * Time: 11:56
 */

namespace Pyz\Client\TermsOfService;


use Pyz\Client\TermsOfService\Zed\TermsOfServiceStub;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class TermsOfServiceFactory extends AbstractFactory
{
    /**
     * @return TermsOfServiceStub
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createTermsOfServiceStub() : TermsOfServiceStub
    {
        return new TermsOfServiceStub(
            $this->getZedServiceClient()
        );
    }

    /**
     * @return ZedRequestClientInterface
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getZedServiceClient() : ZedRequestClientInterface
    {
        return $this
            ->getProvidedDependency(TermsOfServiceDependencyProvider::SERVICE_ZED);
    }
}