<?php
/**
 * Durst - project - CampaignPeriodDataProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 10.06.21
 * Time: 15:51
 */

namespace Pyz\Zed\Campaign\Communication\Form\DataProvider;

use Generated\Shared\Transfer\CampaignPeriodTransfer;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;
use Pyz\Zed\Campaign\Communication\Form\CampaignPeriodForm;

class CampaignPeriodDataProvider
{
    /**
     * @var \Pyz\Zed\Campaign\Business\CampaignFacadeInterface
     */
    protected $facade;

    /**
     * CampaignPeriodDataProvider constructor.
     * @param \Pyz\Zed\Campaign\Business\CampaignFacadeInterface $facade
     */
    public function __construct(
        CampaignFacadeInterface $facade
    )
    {
        $this->facade = $facade;
    }

    /**
     * @param int|null $idCampaignPeriod
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     */
    public function getData(?int $idCampaignPeriod): CampaignPeriodTransfer
    {
        if ($idCampaignPeriod === null) {
            return new CampaignPeriodTransfer();
        }

        return $this
            ->facade
            ->getCampaignPeriodById(
                $idCampaignPeriod
            );
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            CampaignPeriodForm::OPTION_ADVERTISING_MATERIAL_CHOICES => $this->getCampaignAdvertisingMaterialChoices()
        ];
    }

    /**
     * @return array
     */
    protected function getCampaignAdvertisingMaterialChoices(): array
    {
        $materialTransfers = $this
            ->facade
            ->getAllActiveCampaignAdvertisingMaterial();

        $result = [];

        foreach ($materialTransfers as $materialTransfer) {
            $key = sprintf(
                '%s (%s)',
                $materialTransfer
                    ->getCampaignAdvertisingMaterialName(),
                $materialTransfer
                    ->getCampaignAdvertisingMaterialDescription()
            );

            $result[$key] = $materialTransfer
                ->getIdCampaignAdvertisingMaterial();
        }

        return $result;
    }
}
