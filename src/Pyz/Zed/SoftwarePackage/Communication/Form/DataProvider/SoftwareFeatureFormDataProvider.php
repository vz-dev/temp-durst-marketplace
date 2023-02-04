<?php
/**
 * Durst - project - SoftwareFeatureFormDataProvider.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 02.11.18
 * Time: 11:10
 */

namespace Pyz\Zed\SoftwarePackage\Communication\Form\DataProvider;


use Generated\Shared\Transfer\SoftwareFeatureTransfer;
use Pyz\Zed\SoftwarePackage\Business\SoftwarePackageFacadeInterface;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;

class SoftwareFeatureFormDataProvider
{
    /**
     * @var SoftwarePackageFacadeInterface
     */
    protected $facade;

    /**
     * @var SoftwarePackageQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * SoftwarePackageFormDataProvider constructor.
     * @param SoftwarePackageFacadeInterface $facade
     * @param SoftwarePackageQueryContainerInterface $queryContainer
     */
    public function __construct(
        SoftwarePackageFacadeInterface $facade,
        SoftwarePackageQueryContainerInterface $queryContainer
    )
    {
        $this->facade = $facade;
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param int $idSoftwareFeatureForm
     * @return SoftwareFeatureTransfer
     */
    public function getData(int $idSoftwareFeatureForm = null): SoftwareFeatureTransfer
    {
        if ($idSoftwareFeatureForm === null) {
            return new SoftwareFeatureTransfer();
        }

        return $this
            ->facade
            ->getSoftwareFeatureById($idSoftwareFeatureForm);
    }
}