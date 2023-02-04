<?php

namespace Pyz\Client\DriverApp;

use Pyz\Client\DriverApp\Zed\DriverAppStub;
use Spryker\Client\Kernel\AbstractFactory;

class DriverAppFactory extends AbstractFactory
{

    /**
     * @return \Pyz\Client\DriverApp\Zed\DriverAppStubInterface
     */
    public function createZedStub()
    {
        return new DriverAppStub($this->getZedRequestClient());
    }

    /**
     * @return \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected function getZedRequestClient()
    {
        return $this->getProvidedDependency(DriverAppDependencyProvider::CLIENT_ZED_REQUEST);
    }

}
