<?php
/**
 * Durst - project - PriceManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 27.04.18
 * Time: 11:24
 */

namespace Pyz\Zed\MerchantPrice\Business\Manager;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;
use Pyz\Zed\MerchantPrice\Business\Exception\PriceMissingException;
use Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface;
use Spryker\Zed\Price\Business\PriceFacadeInterface;

class PriceManager
{
    /**
     * @var \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Spryker\Zed\Price\Business\PriceFacadeInterface
     */
    protected $priceFacade;

    /**
     * PriceManager constructor.
     *
     * @param \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\Price\Business\PriceFacadeInterface $priceFacade
     */
    public function __construct(
        MerchantPriceQueryContainerInterface $queryContainer,
        PriceFacadeInterface $priceFacade
    ) {
        $this->queryContainer = $queryContainer;
        $this->priceFacade = $priceFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function addPriceToItems(CartChangeTransfer $cartChangeTransfer)
    {
        $cartChangeTransfer->setQuote(
            $this->setQuotePriceMode($cartChangeTransfer->getQuote())
        );

        $cartChangeTransfer->requireBranch();
        $branchTransfer = $cartChangeTransfer->getBranch();

        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
                $this->setPrice($itemTransfer, $branchTransfer);
        }

        return $cartChangeTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return void
     */
    protected function setPrice(ItemTransfer $itemTransfer, BranchTransfer $branchTransfer): void
    {
        $price = $this->findPriceForProductAndBranch($itemTransfer, $branchTransfer);

        $itemTransfer->setMerchantSku($price->getMerchantSku());
        $itemTransfer->setUnitGrossPrice($price->getGrossPrice());
        $itemTransfer->setUnitNetPrice($price->getPrice());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function setQuotePriceMode(QuoteTransfer $quoteTransfer)
    {
        if (!$quoteTransfer->getPriceMode()) {
            $quoteTransfer->setPriceMode($this->priceFacade->getDefaultPriceMode());
        }

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @throws \Pyz\Zed\MerchantPrice\Business\Exception\PriceMissingException
     *
     * @return \Orm\Zed\MerchantPrice\Persistence\MerchantPrice
     */
    protected function findPriceForProductAndBranch(ItemTransfer $itemTransfer, BranchTransfer $branchTransfer): MerchantPrice
    {
        $branchTransfer->requireIdBranch();
        $itemTransfer->requireSku();

        $priceEntity = $this
            ->queryContainer
            ->queryActivePricesByIdBranchAndConcreteSku($branchTransfer->getIdBranch(), $itemTransfer->getSku())
            ->findOne();

        if ($priceEntity === null) {
            throw new PriceMissingException(
                sprintf(
                    PriceMissingException::MESSAGE,
                    $itemTransfer->getSku(),
                    $branchTransfer->getIdBranch()
                )
            );
        }

        return $priceEntity;
    }
}
