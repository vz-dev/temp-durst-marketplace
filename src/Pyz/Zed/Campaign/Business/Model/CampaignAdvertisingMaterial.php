<?php
/**
 * Durst - project - CampaignAdvertisingMaterial.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.06.21
 * Time: 09:39
 */

namespace Pyz\Zed\Campaign\Business\Model;


use Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer;
use Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterial;
use Pyz\Zed\Campaign\Business\Exception\CampaignAdvertisingMaterialNotFoundException;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface;

class CampaignAdvertisingMaterial implements CampaignAdvertisingMaterialInterface
{
    /**
     * @var \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var array|\Pyz\Zed\Campaign\Business\Hydrator\CampaignAdvertisingMaterial\CampaignAdvertisingMaterialHydratorInterface[]
     */
    protected $campaignAdvertisingMaterialHydrators;

    /**
     * CampaignAdvertisingMaterial constructor.
     * @param \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface $queryContainer
     * @param array|\Pyz\Zed\Campaign\Business\Hydrator\CampaignAdvertisingMaterial\CampaignAdvertisingMaterialHydratorInterface[] $campaignAdvertisingMaterialHydrators
     */
    public function __construct(
        CampaignQueryContainerInterface $queryContainer,
        array $campaignAdvertisingMaterialHydrators
    )
    {
        $this->queryContainer = $queryContainer;
        $this->campaignAdvertisingMaterialHydrators = $campaignAdvertisingMaterialHydrators;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignAdvertisingMaterial
     * @param int $idCampaignPeriod
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignAdvertisingMaterialNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCampaignAdvertisingMaterialByIdForPeriod(
        int $idCampaignAdvertisingMaterial,
        int $idCampaignPeriod
    ): CampaignAdvertisingMaterialTransfer
    {
        $campaignAdvertisingMaterial = $this
            ->queryContainer
            ->queryCampaignAdvertisingMaterial()
            ->useDstCampaignPeriodCampaignAdvertisingMaterialQuery()
                ->filterByIdCampaignPeriod(
                    $idCampaignPeriod
                )
                ->filterByIdCampaignAdvertisingMaterial(
                    $idCampaignAdvertisingMaterial
                )
            ->endUse()
            ->findOne();

        if ($campaignAdvertisingMaterial === null) {
            throw new CampaignAdvertisingMaterialNotFoundException(
                sprintf(
                    CampaignAdvertisingMaterialNotFoundException::MESSAGE,
                    $idCampaignAdvertisingMaterial,
                    $idCampaignPeriod
                )
            );
        }

        return $this
            ->entityToTransfer(
                $campaignAdvertisingMaterial,
                $idCampaignPeriod
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignAdvertisingMaterial
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignAdvertisingMaterialNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCampaignAdvertisingMaterialById(
        int $idCampaignAdvertisingMaterial
    ): CampaignAdvertisingMaterialTransfer
    {
        $campaignAdvertisingMaterial = $this
            ->queryContainer
            ->queryCampaignAdvertisingMaterial()
            ->filterByIdCampaignAdvertisingMaterial(
                $idCampaignAdvertisingMaterial
            )
            ->findOne();

        if ($campaignAdvertisingMaterial === null) {
            throw new CampaignAdvertisingMaterialNotFoundException(
                sprintf(
                    CampaignAdvertisingMaterialNotFoundException::MESSAGE_NO_CAMPAIGN_PERIOD,
                    $idCampaignAdvertisingMaterial
                )
            );
        }

        return $this
            ->entityToTransfer(
                $campaignAdvertisingMaterial
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function addCampaignAdvertisingMaterial(
        CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
    ): CampaignAdvertisingMaterialTransfer
    {
        $entity = $this
            ->transferToEntity(
                $advertisingMaterialTransfer
            );

        $entity
            ->save();

        return $this
            ->entityToTransfer(
                $entity
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignAdvertisingMaterial
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignAdvertisingMaterialNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function activateCampaignAdvertisingMaterial(int $idCampaignAdvertisingMaterial): bool
    {
        $campaignAdvertisingMaterial = $this
            ->queryContainer
            ->queryCampaignAdvertisingMaterial()
            ->filterByIdCampaignAdvertisingMaterial(
                $idCampaignAdvertisingMaterial
            )
            ->findOne();

        if ($campaignAdvertisingMaterial === null) {
            throw new CampaignAdvertisingMaterialNotFoundException(
                sprintf(
                    CampaignAdvertisingMaterialNotFoundException::MESSAGE_NO_CAMPAIGN_PERIOD,
                    $idCampaignAdvertisingMaterial
                )
            );
        }

        $rows = $campaignAdvertisingMaterial
            ->setIsActive(
                true
            )
            ->save();

        return ($rows === 1);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignAdvertisingMaterial
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignAdvertisingMaterialNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function deactivateCampaignAdvertisingMaterial(int $idCampaignAdvertisingMaterial): bool
    {
        $campaignAdvertisingMaterial = $this
            ->queryContainer
            ->queryCampaignAdvertisingMaterial()
            ->filterByIdCampaignAdvertisingMaterial(
                $idCampaignAdvertisingMaterial
            )
            ->findOne();

        if ($campaignAdvertisingMaterial === null) {
            throw new CampaignAdvertisingMaterialNotFoundException(
                sprintf(
                    CampaignAdvertisingMaterialNotFoundException::MESSAGE_NO_CAMPAIGN_PERIOD,
                    $idCampaignAdvertisingMaterial
                )
            );
        }

        $rows = $campaignAdvertisingMaterial
            ->setIsActive(
                false
            )
            ->save();

        return ($rows === 1);
    }

    /**
     * {@inheritDoc}
     *
     * @return array|CampaignAdvertisingMaterialTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getAllActiveCampaignAdvertisingMaterial(): array
    {
        $materials = $this
            ->queryContainer
            ->queryCampaignAdvertisingMaterial()
            ->filterByIsActive(true)
            ->orderByCampaignAdvertisingMaterialName()
            ->find();

        $result = [];

        foreach ($materials as $material) {
            $result[] = $this
                ->entityToTransfer(
                    $material
                );
        }

        return $result;
    }

    /**
     * @param \Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterial $advertisingMaterial
     * @param int|null $idCampaignPeriod
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     */
    protected function entityToTransfer(
        DstCampaignAdvertisingMaterial $advertisingMaterial,
        ?int $idCampaignPeriod = null
    ): CampaignAdvertisingMaterialTransfer
    {
        $transfer = (new CampaignAdvertisingMaterialTransfer())
            ->fromArray(
                $advertisingMaterial
                    ->toArray(),
                true
            )
            ->setFkCampaignPeriod(
                $idCampaignPeriod
            );

        foreach ($this->campaignAdvertisingMaterialHydrators as $campaignAdvertisingMaterialHydrator) {
            $campaignAdvertisingMaterialHydrator
                ->hydrateCampaignAdvertisingMaterial(
                    $transfer
                );
        }

        return $transfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterial
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function transferToEntity(
        CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
    ): DstCampaignAdvertisingMaterial
    {
        $entity = $this
            ->findEntityOrCreate(
                $advertisingMaterialTransfer
            );

        $entity
            ->fromArray(
                $advertisingMaterialTransfer
                    ->toArray()
            );

        return $entity;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterial
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function findEntityOrCreate(
        CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
    ): DstCampaignAdvertisingMaterial
    {
        if ($advertisingMaterialTransfer->getIdCampaignAdvertisingMaterial() === null) {
            return new DstCampaignAdvertisingMaterial();
        }

        return $this
            ->queryContainer
            ->queryCampaignAdvertisingMaterial()
            ->filterByIdCampaignAdvertisingMaterial(
                $advertisingMaterialTransfer
                    ->getIdCampaignAdvertisingMaterial()
            )
            ->findOneOrCreate();
    }
}
