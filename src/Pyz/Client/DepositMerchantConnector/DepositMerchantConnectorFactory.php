<?php
/**
 * Durst - project - DepositMerchantFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-29
 * Time: 12:40
 */

namespace Pyz\Client\DepositMerchantConnector;

use Pyz\Client\DepositMerchantConnector\Zed\DepositMerchantConnectorStub;
use Pyz\Client\DepositMerchantConnector\Zed\DepositMerchantConnectorStubInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class DepositMerchantConnectorFactory extends AbstractFactory
{
    /**
     * @return \Pyz\Client\DepositMerchantConnector\Zed\DepositMerchantConnectorStubInterface
     */
    public function createDepositMerchantConnectorStub(): DepositMerchantConnectorStubInterface
    {
        return new DepositMerchantConnectorStub(
            $this->getZedRequestClient()
        );
    }

    /**
     * @return \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected function getZedRequestClient(): ZedRequestClientInterface
    {
        return $this
            ->getProvidedDependency(
                DepositMerchantConnectorDependencyProvider::CLIENT_ZED_REQUEST
            );
    }
}
