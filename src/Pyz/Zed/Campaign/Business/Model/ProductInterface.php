<?php
/**
 * Durst - project - ProductInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.07.21
 * Time: 15:11
 */

namespace Pyz\Zed\Campaign\Business\Model;


use DateTime;
use Generated\Shared\Transfer\PossibleCampaignProductTransfer;

interface ProductInterface
{
    /**
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @param string $sku
     * @param array $exceptions
     * @return array|\Generated\Shared\Transfer\PossibleCampaignProductTransfer[]
     */
    public function findAvailableProductsForCampaign(
        int $idCampaignPeriod,
        int $idBranch,
        string $sku,
        array $exceptions
    ): array;

    /**
     * @param \DateTime $validFrom
     * @param \DateTime $validTo
     * @param int $idBranch
     * @param string $sku
     * @param array $exceptions
     * @return array
     */
    public function findAvailableProductsForDateRange(
        DateTime $validFrom,
        DateTime $validTo,
        int $idBranch,
        string $sku,
        array $exceptions
    ): array;

    /**
     * @param int $idBranch
     * @param string $sku
     * @return \Generated\Shared\Transfer\PossibleCampaignProductTransfer
     */
    public function getProductBySkuForBranch(
        int $idBranch,
        string $sku
    ): PossibleCampaignProductTransfer;
}
