<?php
/**
 * Durst - project - SoapRequestCommunicationFactory.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-10-30
 * Time: 17:25
 */

namespace Pyz\Zed\SoapRequest\Communication;


use Pyz\Service\SoapRequest\SoapRequestServiceInterface;
use Pyz\Zed\Integra\Business\IntegraFacadeInterface;
use Pyz\Zed\SoapRequest\Communication\Table\SoapRequestTable;
use Pyz\Zed\SoapRequest\Persistence\SoapRequestQueryContainerInterface;
use Pyz\Zed\SoapRequest\SoapRequestDependencyProvider;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

/**
 * Class SoapRequestCommunicationFactory
 * @package Pyz\Zed\SoapRequest\Communication
 * @method SoapRequestQueryContainerInterface getQueryContainer()
 */
class SoapRequestCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return SoapRequestServiceInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createServiceSoapRequest() : SoapRequestServiceInterface
    {
        return $this
            ->getProvidedDependency(SoapRequestDependencyProvider::SERVICE_SOAP_REQUEST);
    }

    /**
     * @return SoapRequestTable
     */
    public function createSoapRequestTable() : SoapRequestTable
    {
        return new SoapRequestTable(
            $this->getQueryContainer()
        );
    }

    /**
     * @return IntegraFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createFacadeIntegra() : IntegraFacadeInterface
    {
        return $this
            ->getProvidedDependency(SoapRequestDependencyProvider::FACADE_INTEGRA);
    }

}
