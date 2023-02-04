<?php
/**
 * Durst - project - CampaignAdvertisingMaterialDataProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 10.06.21
 * Time: 09:33
 */

namespace Pyz\Zed\Campaign\Communication\Form\DataProvider;


use Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;

class CampaignAdvertisingMaterialDataProvider
{
    /**
     * @var \Pyz\Zed\Campaign\Business\CampaignFacadeInterface
     */
    protected $facade;

    /**
     * CampaignAdvertisingMaterialDataProvider constructor.
     * @param \Pyz\Zed\Campaign\Business\CampaignFacadeInterface $facade
     */
    public function __construct(
        CampaignFacadeInterface $facade
    )
    {
        $this->facade = $facade;
    }

    /**
     * @param int|null $idCampaignAdvertisingMaterial
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     */
    public function getData(?int $idCampaignAdvertisingMaterial): CampaignAdvertisingMaterialTransfer
    {
        if ($idCampaignAdvertisingMaterial === null) {
            return new CampaignAdvertisingMaterialTransfer();
        }

        return $this
            ->facade
            ->getCampaignAdvertisingMaterialById(
                $idCampaignAdvertisingMaterial
            );
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [];
    }
}
