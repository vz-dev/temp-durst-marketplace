<?php
/**
 * Durst - project - MerchantFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 15:50
 */

namespace Pyz\Client\Merchant;

use Pyz\Client\Merchant\Zed\MerchantStub;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class MerchantFactory extends AbstractFactory
{
    /**
     * @return \Pyz\Client\Merchant\Zed\MerchantStub
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createMerchantStub(): MerchantStub
    {
        return new MerchantStub(
            $this->getZedService()
        );
    }

    /**
     * @return \Spryker\Client\ZedRequest\ZedRequestClientInterface
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getZedService(): ZedRequestClientInterface
    {
        return $this
            ->getProvidedDependency(
                MerchantDependencyProvider::SERVICE_ZED
            );
    }
}
