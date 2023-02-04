<?php
/**
 * Durst - project - ProductRepositoryInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.11.20
 * Time: 16:53
 */

namespace Pyz\Zed\Integra\Business\Model\Quote\Product;


interface ProductRepositoryInterface
{
    /**
     * @param string $merchantSku
     * @param string $unitType
     * @param int $idBranch
     *
     * @return string
     */
    public function getSkuForMerchantSku(string $merchantSku, string $unitType, int $idBranch): string;

    /**
     * @param int $idBranch
     * @param array $merchantSkus
     *
     * @return array
     */
    public function loadSkus(
        int $idBranch,
        array $merchantSkus
    ): array;
}
