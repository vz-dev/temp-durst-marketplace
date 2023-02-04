<?php
/**
 * Durst - project - PriceImportToMerchantPriceBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 11:01
 */

namespace Pyz\Zed\PriceImport\Dependency\Facade;


use Generated\Shared\Transfer\PriceTransfer;
use Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface;

class PriceImportToMerchantPriceBridge implements PriceImportToMerchantPriceBridgeInterface
{
    /**
     * @var \Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface
     */
    protected $merchantPriceFacade;

    /**
     * PriceImportToMerchantPriceBridge constructor.
     * @param \Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface $merchantPriceFacade
     */
    public function __construct(MerchantPriceFacadeInterface $merchantPriceFacade)
    {
        $this->merchantPriceFacade = $merchantPriceFacade;
    }

    /**
     * {@inheritDoc}
     *
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

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\PriceTransfer $priceTransfer
     * @return \Generated\Shared\Transfer\PriceTransfer | bool
     */
    public function importPriceForBranch(PriceTransfer $priceTransfer)
    {
        return $this
            ->merchantPriceFacade
            ->importPriceForBranch(
                $priceTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idPrice
     * @param int $idBranch
     */
    public function removePriceFromBranch(
        int $idPrice,
        int $idBranch
    ) {
        $this
            ->merchantPriceFacade
            ->removePriceFromBranch(
                $idPrice,
                $idBranch
            );
    }
}
