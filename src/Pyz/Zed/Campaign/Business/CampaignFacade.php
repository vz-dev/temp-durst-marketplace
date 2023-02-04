<?php
/**
 * Durst - project - CampaignFacade.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.06.21
 * Time: 12:08
 */

namespace Pyz\Zed\Campaign\Business;

use DateTime;
use Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer;
use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;
use Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer;
use Generated\Shared\Transfer\CampaignPeriodTransfer;
use Generated\Shared\Transfer\MerchantCampaignOrderTransfer;
use Generated\Shared\Transfer\PossibleCampaignProductTransfer;
use Orm\Zed\Campaign\Persistence\Base\DstCampaignPeriod;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrder;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderQuery;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class CampaignFacade
 * @package Pyz\Zed\Campaign\Business
 * @method CampaignBusinessFactory getFactory()
 */
class CampaignFacade extends AbstractFacade implements CampaignFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCampaignPeriodById(int $idCampaignPeriod): CampaignPeriodTransfer
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodModel()
            ->getCampaignPeriodById(
                $idCampaignPeriod
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function saveCampaignPeriod(CampaignPeriodTransfer $campaignPeriodTransfer): CampaignPeriodTransfer
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodModel()
            ->saveCampaignPeriod(
                $campaignPeriodTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $branchOrderTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function saveCampaignPeriodBranchOrder(
        CampaignPeriodBranchOrderTransfer $branchOrderTransfer
    ): CampaignPeriodBranchOrderTransfer
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodBranchOrderModel()
            ->saveCampaignPeriodBranchOrder(
                $branchOrderTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function updateCampaignPeriodBranchOrder(
        CampaignPeriodBranchOrderTransfer $campaignPeriodBranchOrderTransfer
    ): CampaignPeriodBranchOrderTransfer
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodBranchOrderModel()
            ->updateCampaignPeriodBranchOrder(
                $campaignPeriodBranchOrderTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignAdvertisingMaterial
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCampaignAdvertisingMaterialById(
        int $idCampaignAdvertisingMaterial
    ): CampaignAdvertisingMaterialTransfer
    {
        return $this
            ->getFactory()
            ->createCampaignAdvertisingMaterialModel()
            ->getCampaignAdvertisingMaterialById(
                $idCampaignAdvertisingMaterial
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignAdvertisingMaterial
     * @param int $idCampaignPeriod
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCampaignAdvertisingMaterialByIdForPeriod(
        int $idCampaignAdvertisingMaterial,
        int $idCampaignPeriod
    ): CampaignAdvertisingMaterialTransfer
    {
        return $this
            ->getFactory()
            ->createCampaignAdvertisingMaterialModel()
            ->getCampaignAdvertisingMaterialByIdForPeriod(
                $idCampaignAdvertisingMaterial,
                $idCampaignPeriod
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return array|CampaignAdvertisingMaterialTransfer[]
     */
    public function getAllActiveCampaignAdvertisingMaterial(): array
    {
        return $this
            ->getFactory()
            ->createCampaignAdvertisingMaterialModel()
            ->getAllActiveCampaignAdvertisingMaterial();
    }

    /**#
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     */
    public function addCampaignAdvertisingMaterial(
        CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
    ): CampaignAdvertisingMaterialTransfer
    {
        return $this
            ->getFactory()
            ->createCampaignAdvertisingMaterialModel()
            ->addCampaignAdvertisingMaterial(
                $advertisingMaterialTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function activateCampaignPeriod(
        int $idCampaignPeriod
    ): bool
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodModel()
            ->activateCampaignPeriod(
                $idCampaignPeriod
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function deactivateCampaignPeriod(int $idCampaignPeriod): bool
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodModel()
            ->deactivateCampaignPeriod(
                $idCampaignPeriod
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignAdvertisingMaterial
     * @return bool
     */
    public function activateCampaignAdvertisingMaterial(
        int $idCampaignAdvertisingMaterial
    ): bool
    {
        return $this
            ->getFactory()
            ->createCampaignAdvertisingMaterialModel()
            ->activateCampaignAdvertisingMaterial(
                $idCampaignAdvertisingMaterial
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignAdvertisingMaterial
     * @return bool
     */
    public function deactivateCampaignAdvertisingMaterial(
        int $idCampaignAdvertisingMaterial
    ): bool
    {
        return $this
            ->getFactory()
            ->createCampaignAdvertisingMaterialModel()
            ->deactivateCampaignAdvertisingMaterial(
                $idCampaignAdvertisingMaterial
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int|null $idCampaignPeriod
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getDatesWithCampaigns(
        ?int $idCampaignPeriod
    ): array
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodModel()
            ->getDatesWithCampaigns(
                $idCampaignPeriod
            );
    }

    /**
     * @return array|\Pyz\Zed\Campaign\Business\Validator\CampaignPeriod\CampaignPeriodValidatorInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCampaignPeriodValidators(): array
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodValidators();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @return array|\Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getProductsForCampaignAndBranch(
        int $idCampaignPeriod,
        int $idBranch
    ): array
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodBranchOrderProductModel()
            ->getProductsForCampaignAndBranch(
                $idCampaignPeriod,
                $idBranch
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriodBranchOrder
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCampaignPeriodBranchOrderById(
        int $idCampaignPeriodBranchOrder
    ): CampaignPeriodBranchOrderTransfer
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodBranchOrderModel()
            ->getCampaignPeriodBranchOrderById(
                $idCampaignPeriodBranchOrder
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return array|CampaignPeriodTransfer[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCampaignPeriodList(): array
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodModel()
            ->getCampaignPeriodList();
    }

    /**
     * @param int $idBranch
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getAvailableCampaignPeriodsForBranch(
        int $idBranch
    ): array
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodModel()
            ->getAvailableCampaignPeriodsForBranch(
                $idBranch
            );
    }

    /**
     * @param int $idBranch
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getAllAvailableCampaignPeriods(): array
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodModel()
            ->getAllAvailableCampaignPeriods();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCampaignPeriodBranchOrdersForBranch(
        int $idBranch
    ): array
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodBranchOrderModel()
            ->getCampaignPeriodBranchOrdersForBranch(
                $idBranch
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function isCampaignPeriodOrderedByBranch(
        int $idCampaignPeriod,
        int $idBranch
    ): bool
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodBranchOrderModel()
            ->isCampaignPeriodOrderedByBranch(
                $idCampaignPeriod,
                $idBranch
            );
    }

    /**
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function saveIsBookableForCampaign()
    {
        $campaignEntities = $this
            ->getAllAvailableCampaignPeriods();

        foreach ($campaignEntities as $campaignEntity) {
            $campaignTransfer = $this
                ->entityToTransfer($campaignEntity);

            $campaignEntity->setIsBookable(
                $campaignTransfer->getBookable()
            );

            $campaignEntity->save();
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @param int|null $idCampaignPeriodBranchOrder
     * @return \Generated\Shared\Transfer\MerchantCampaignOrderTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchantCampaignOrderById(
        int $idCampaignPeriod,
        int $idBranch,
        ?int $idCampaignPeriodBranchOrder = null
    ): MerchantCampaignOrderTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantCampaignOrderModel()
            ->getMerchantCampaignOrderById(
                $idCampaignPeriod,
                $idBranch,
                $idCampaignPeriodBranchOrder
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCampaignPeriodBranchOrderFromMerchantCampaignOrder(
        MerchantCampaignOrderTransfer $merchantCampaignOrderTransfer
    ): CampaignPeriodBranchOrderTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantCampaignOrderModel()
            ->createCampaignPeriodBranchOrderFromMerchantCampaignOrder(
                $merchantCampaignOrderTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriodBranchOrderProduct
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignPeriodBranchOrderProductNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCampaignOrderProductById(
        int $idCampaignPeriodBranchOrderProduct
    ): CampaignPeriodBranchOrderProductTransfer
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodBranchOrderProductModel()
            ->getCampaignOrderProductById(
                $idCampaignPeriodBranchOrderProduct
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @param string $sku
     * @param array $exceptions
     * @return array|\Generated\Shared\Transfer\PossibleCampaignProductTransfer[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function findAvailableProductsForCampaign(
        int $idCampaignPeriod,
        int $idBranch,
        string $sku,
        array $exceptions
    ): array
    {
        return $this
            ->getFactory()
            ->createProductModel()
            ->findAvailableProductsForCampaign(
                $idCampaignPeriod,
                $idBranch,
                $sku,
                $exceptions
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \DateTime $validFrom
     * @param \DateTime $validTo
     * @param int $idBranch
     * @param string $sku
     * @param array $exceptions
     * @return array|PossibleCampaignProductTransfer[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function findAvailableProductsForDateRange(
        DateTime $validFrom,
        DateTime $validTo,
        int $idBranch,
        string $sku,
        array $exceptions
    ): array
    {
        return $this
            ->getFactory()
            ->createProductModel()
            ->findAvailableProductsForDateRange(
                $validFrom,
                $validTo,
                $idBranch,
                $sku,
                $exceptions
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param string $sku
     * @return \Generated\Shared\Transfer\PossibleCampaignProductTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getProductBySkuForBranch(
        int $idBranch,
        string $sku
    ): PossibleCampaignProductTransfer
    {
        return $this
            ->getFactory()
            ->createProductModel()
            ->getProductBySkuForBranch(
                $idBranch,
                $sku
            );
    }

    public function entityToTransfer(
        DstCampaignPeriod $campaignPeriod
    ) {
        return $this
            ->getFactory()
            ->createCampaignPeriodModel()
            ->entityToTransfer(
                $campaignPeriod
            );
    }

    public function entityToTransferCampaignPeriodBranchOrder(
        DstCampaignPeriodBranchOrder $branchOrder
    ): CampaignPeriodBranchOrderTransfer
    {
        return $this
            ->getFactory()
            ->createCampaignPeriodBranchOrderModel()
            ->entityToTransfer(
                $branchOrder
            );
    }
}
