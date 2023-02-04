<?php
/**
 * Durst - project - ProductExportToMerchantPriceBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 17:01
 */

namespace Pyz\Zed\ProductExport\Dependency\Facade;


use Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface;

class ProductExportToMerchantPriceBridge implements ProductExportToMerchantPriceBridgeInterface
{
    /**
     * @var \Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface
     */
    protected $merchantPriceFacade;

    /**
     * ProductExportToMerchantPriceBridge constructor.
     * @param \Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface $merchantPriceFacade
     */
    public function __construct(
        MerchantPriceFacadeInterface $merchantPriceFacade
    )
    {
        $this->merchantPriceFacade = $merchantPriceFacade;
    }

    /**
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\PriceTransfer[]
     */
    public function getPricesForBranch(int $idBranch): array
    {
        return $this
            ->merchantPriceFacade
            ->getPricesForBranch(
                $idBranch
            );
    }
}
