<?php


namespace Pyz\Client\Tour;


use Pyz\Client\Tour\Zed\TourStub;
use Pyz\Client\Tour\Zed\TourStubInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class TourFactory extends AbstractFactory
{
    /**
     * @return \Pyz\Client\Tour\Zed\TourStubInterface
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createTourStub(): TourStubInterface
    {
        return new TourStub(
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
            ->getProvidedDependency(TourDependencyProvider::SERVICE_ZED);
    }
}