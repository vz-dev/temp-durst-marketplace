<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 21.02.18
 * Time: 10:00
 */

namespace Pyz\Client\AppRestApi;

use Pyz\Client\AppRestApi\Search\SearchStub;
use Pyz\Client\AppRestApi\Search\SearchStubInterface;
use Pyz\Client\AppRestApi\Storage\StorageStub;
use Pyz\Client\AppRestApi\Storage\StorageStubInterface;
use Pyz\Client\AppRestApi\Zed\AppRestApiStub;
use Pyz\Client\Search\SearchClientInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\Storage\StorageClientInterface;

/**
 * Class AppRestApiFactory
 * @package Pyz\Client\AppRestApi
 * @method \Pyz\Client\AppRestApi\AppRestApiConfig getConfig()
 */
class AppRestApiFactory extends AbstractFactory
{
    /**
     * @return \Pyz\Client\AppRestApi\Zed\AppRestApiStub
     */
    public function createAppRestApiStub()
    {
        return new AppRestApiStub(
            $this->getZedService()
        );
    }

    /**
     * @return \Pyz\Client\AppRestApi\Search\SearchStub
     */
    public function createSearchStub(): SearchStubInterface
    {
        return new SearchStub(
            $this->getSearchClient(),
            $this->getConfig()
        );
    }

    /**
     * @return \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected function getZedService()
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::SERVICE_ZED);
    }

    /**
     * @return \Pyz\Client\Search\SearchClientInterface
     */
    protected function getSearchClient(): SearchClientInterface
    {
        return $this
            ->getProvidedDependency(
                AppRestApiDependencyProvider::CLIENT_SEARCH
            );
    }

    /**
     * @return StorageStubInterface
     */
    public function createStorageStub(): StorageStubInterface
    {
        return new StorageStub(
            $this->getStorageClient(),
            $this->getConfig()
        );
    }

    /**
     * @return \Spryker\Client\Storage\StorageClientInterface
     */
    protected function getStorageClient(): StorageClientInterface
    {
        return $this
            ->getProvidedDependency(
                AppRestApiDependencyProvider::CLIENT_STORAGE
            );
    }
}
