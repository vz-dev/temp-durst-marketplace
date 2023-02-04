<?php
/**
 * Durst - project - HeidelpayRestFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 15.01.19
 * Time: 15:09
 */

namespace Pyz\Client\HeidelpayRest;

use Pyz\Client\HeidelpayRest\Zed\HeidelpayRestZedStub;
use Pyz\Client\HeidelpayRest\Zed\HeidelpayRestZedStubInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class HeidelpayRestFactory extends AbstractFactory
{
    /**
     * @return \Pyz\Client\HeidelpayRest\Zed\HeidelpayRestZedStubInterface
     */
    public function createHeidelpayRestZedStub(): HeidelpayRestZedStubInterface
    {
        return new HeidelpayRestZedStub(
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
                HeidelpayRestDependencyProvider::CLIENT_ZED_REQUEST
            );
    }
}
