<?php
/**
 * Durst - project - ReleaseTypeDataProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-06
 * Time: 11:46
 */

namespace Pyz\Zed\DriverApp\Communication\Form\DataProvider;

use Generated\Shared\Transfer\DriverAppReleaseTransfer;
use Pyz\Zed\DriverApp\Business\DriverAppFacadeInterface;
use Pyz\Zed\DriverApp\Communication\Form\ReleaseType;
use Pyz\Zed\DriverApp\DriverAppConfig;

class ReleaseTypeDataProvider
{
    /**
     * @var \Pyz\Zed\DriverApp\DriverAppConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\DriverApp\Business\DriverAppFacadeInterface
     */
    protected $facade;

    /**
     * ReleaseTypeDataProvider constructor.
     *
     * @param \Pyz\Zed\DriverApp\DriverAppConfig $config
     * @param \Pyz\Zed\DriverApp\Business\DriverAppFacadeInterface $facade
     */
    public function __construct(
        DriverAppConfig $config,
        DriverAppFacadeInterface $facade
    ) {
        $this->config = $config;
        $this->facade = $facade;
    }

    /**
     * @param int|null $idDriverAppRelease
     *
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function getData(?int $idDriverAppRelease): DriverAppReleaseTransfer
    {
        if ($idDriverAppRelease == null) {
            return new DriverAppReleaseTransfer();
        }

        return $this
            ->facade
            ->getReleaseById($idDriverAppRelease);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            ReleaseType::OPTION_UPLOAD_FILE_PATH => $this->config->getUploadPath(),
        ];
    }
}
