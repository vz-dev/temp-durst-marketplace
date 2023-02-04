<?php
/**
 * Durst - project - OmsFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 23:01
 */

namespace Pyz\Client\Oms;

use Pyz\Client\Oms\Zed\OmsStub;
use Pyz\Client\Oms\Zed\OmsStubInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class OmsFactory extends AbstractFactory
{
    /**
     * @return \Pyz\Client\Oms\Zed\OmsStubInterface
     */
    public function createOmsStub(): OmsStubInterface
    {
        return new OmsStub(
            $this->getZedRequestClient()
        );
    }

    /**
     *
     * @return \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected function getZedRequestClient(): ZedRequestClientInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::CLIENT_ZED_REQUEST
            );
    }
}
