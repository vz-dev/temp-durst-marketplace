<?php
/**
 * Durst - project - CampaignPeriodBranchOrderProductAdvertisingMaterialHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 21.06.21
 * Time: 14:40
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct;

use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface;

class CampaignPeriodBranchOrderProductAdvertisingMaterialHydrator implements CampaignPeriodBranchOrderProductHydratorInterface
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
     * CampaignPeriodBranchOrderProductAdvertisingMaterialHydrator constructor.
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
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return void
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hydrateCampaignPeriodBranchOrderProduct(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): void
    {
        $materials = $this
            ->queryContainer
            ->queryCampaignAdvertisingMaterial()
            ->useDstCampaignBranchProductCampaignAdvertisingMaterialQuery()
                ->filterByFkCampaignPeriodBranchOrderProduct(
                    $campaignPeriodBranchOrderProductTransfer
                        ->getIdCampaignPeriodBranchOrderProduct()
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
                    $campaignPeriodBranchOrderProductTransfer
                        ->getFkCampaignPeriod()
                );

            $campaignPeriodBranchOrderProductTransfer
                ->addAssignedCampaignAdvertisingMaterial(
                    $materialTransfer
                )
                ->addCampaignAdvertisingMaterials(
                    $material
                        ->getIdCampaignAdvertisingMaterial()
                );
        }
    }
}
