<?php

namespace Pyz\Zed\Easybill\Business;

use Pyz\Zed\Easybill\Business\Queue\InvoiceQueueManager;
use Pyz\Zed\Easybill\Business\Queue\InvoiceQueueManagerInterface;
use Pyz\Zed\Easybill\Business\Resource\Customer;
use Pyz\Zed\Easybill\Business\Resource\CustomerInterface;
use Pyz\Zed\Easybill\Business\Resource\Document;
use Pyz\Zed\Easybill\Business\Resource\DocumentInterface;
use Pyz\Zed\Easybill\Business\Resource\ResourceManager;
use Pyz\Zed\Easybill\Business\Resource\ResourceManagerInterface;
use Pyz\Zed\Easybill\Dependency\Client\EasybillToQueueBridgeInterface;
use Pyz\Zed\Easybill\Dependency\Service\EasybillToHttpRequestBridgeInterface;
use Pyz\Zed\Easybill\EasybillDependencyProvider;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \Pyz\Zed\Easybill\EasybillConfig getConfig()
 */
class EasybillBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\Easybill\Business\Resource\ResourceManagerInterface
     */
    public function createResourceManager(): ResourceManagerInterface
    {
        return new ResourceManager(
            $this->createDocumentResource(),
            $this->createCustomerResource()
        );
    }

    /**
     * @return \Pyz\Zed\Easybill\Business\Resource\DocumentInterface
     */
    protected function createDocumentResource(): DocumentInterface
    {
        return new Document(
            $this->getHttpRequestService(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Easybill\Business\Resource\CustomerInterface
     */
    protected function createCustomerResource(): CustomerInterface
    {
        return new Customer(
            $this->getHttpRequestService(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Easybill\Business\Queue\InvoiceQueueManagerInterface
     */
    public function createInvoiceQueueManager(): InvoiceQueueManagerInterface
    {
        return new InvoiceQueueManager(
            $this->getQueueClient()
        );
    }

    /**
     * @return \Pyz\Zed\Easybill\Dependency\Service\EasybillToHttpRequestBridgeInterface
     */
    protected function getHttpRequestService(): EasybillToHttpRequestBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                EasybillDependencyProvider::SERVICE_HTTP_REQUEST
            );
    }

    /**
     * @return \Pyz\Zed\Easybill\Dependency\Client\EasybillToQueueBridgeInterface
     */
    protected function getQueueClient(): EasybillToQueueBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                EasybillDependencyProvider::CLIENT_QUEUE
            );
    }
}
