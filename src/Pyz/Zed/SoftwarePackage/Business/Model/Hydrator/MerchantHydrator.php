<?php
/**
 * Durst - project - MerchantHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 14:07
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model\Hydrator;


use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;
use Pyz\Zed\SoftwarePackage\Business\Model\SoftwarePackageInterface;

class MerchantHydrator implements MerchantHydratorInterface
{
    /**
     * @var SoftwarePackageInterface
     */
    protected $softwarePackageModel;

    /**
     * MerchantHydrator constructor.
     * @param SoftwarePackageInterface $softwarePackageModel
     */
    public function __construct(SoftwarePackageInterface $softwarePackageModel)
    {
        $this->softwarePackageModel = $softwarePackageModel;
    }

    /**
     * @param SpyMerchant $merchantEntity
     * @param MerchantTransfer $merchantTransfer
     */
    public function hydrateMerchantBySoftwarePackage(
        SpyMerchant $merchantEntity,
        MerchantTransfer $merchantTransfer
    )
    {
        $softwarePackageTransfer = $this
            ->softwarePackageModel
            ->getSoftwarePackageById($merchantEntity->getFkSoftwarePackage());

        $merchantTransfer->setSoftwarePackage($softwarePackageTransfer);
    }


}