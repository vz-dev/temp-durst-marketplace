<?php
/**
 * Durst - project - CampaignPeriodCampaignAdvertisingMaterialHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.06.21
 * Time: 13:47
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriod;


use Generated\Shared\Transfer\CampaignPeriodTransfer;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface;

class CampaignPeriodCampaignAdvertisingMaterialHydrator implements CampaignPeriodHydratorInterface
{
    /**
     * @var \Pyz\Zed\Campaign\Business\CampaignFacadeInterface
     */
    protected $campaignFacade;

    /**
     * @var \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * CampaignPeriodCampaignAdvertisingMaterialHydrator constructor.
     * @param \Pyz\Zed\Campaign\Business\CampaignFacadeInterface $campaignFacade
     * @param \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface $queryContainer
     */
    public function __construct(
        CampaignFacadeInterface $campaignFacade,
        CampaignQueryContainerInterface $queryContainer
    )
    {
        $this->campaignFacade = $campaignFacade;
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     * @return void
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hydrateCampaignPeriod(CampaignPeriodTransfer $campaignPeriodTransfer): void
    {
        $materials = $this
            ->queryContainer
            ->queryCampaignAdvertisingMaterial()
            ->useDstCampaignPeriodCampaignAdvertisingMaterialQuery()
                ->filterByIdCampaignPeriod(
                    $campaignPeriodTransfer
                        ->getIdCampaignPeriod()
                )
            ->endUse()
            ->orderByCampaignAdvertisingMaterialName()
            ->find();

        foreach ($materials as $material) {
            $materialTransfer = $this
                ->campaignFacade
                ->getCampaignAdvertisingMaterialByIdForPeriod(
                    $material
                        ->getIdCampaignAdvertisingMaterial(),
                    $campaignPeriodTransfer
                        ->getIdCampaignPeriod()
                );

            $campaignPeriodTransfer
                ->addAssignedCampaignAdvertisingMaterials(
                    $materialTransfer
                )
                ->addCampaignAdvertisingMaterials(
                    $material
                        ->getIdCampaignAdvertisingMaterial()
                );
        }
    }
}
