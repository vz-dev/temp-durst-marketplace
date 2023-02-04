<?php
/**
 * Durst - project - StorageStub.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 18.10.21
 * Time: 20:29
 */

namespace Pyz\Client\AppRestApi\Storage;


use Spryker\Client\Storage\StorageClientInterface;

class StorageStub implements StorageStubInterface
{
    /**
     * @var StorageClientInterface
     */
    protected $storageClient;

    /**
     * @var \Pyz\Client\AppRestApi\AppRestApiConfig
     */
    protected $config;


    public function __construct(
        StorageClientInterface $searchClient,
        $config
    ) {
        $this->storageClient = $searchClient;
        $this->config = $config;
    }

    /**
     * @param int $idBranch
     * @return array
     */
    public function getGMSettings(int $idBranch) : array
    {
        return $this
            ->storageClient
            ->get($this->getSettingKeyForBranch($idBranch));
    }

    /**
     * @param int $idBranch
     * @return string
     */
    protected function getSettingKeyForBranch(int $idBranch): string
    {
        return sprintf('de.de_de.resource.gm_settings.%s', $idBranch);
    }

}
