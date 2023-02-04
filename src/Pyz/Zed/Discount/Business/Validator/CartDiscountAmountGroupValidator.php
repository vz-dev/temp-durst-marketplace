<?php
/**
 * Durst - project - DiscountAmountValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.05.21
 * Time: 09:32
 */

namespace Pyz\Zed\Discount\Business\Validator;


use Generated\Shared\Transfer\CartDiscountGroupValidationTransfer;
use Pyz\Zed\Discount\Business\Exception\DiscountPriceLowerThanProductPriceException;
use Pyz\Zed\Product\Persistence\ProductQueryContainer;
use Spryker\Zed\Discount\Dependency\Facade\DiscountToMoneyInterface;

class CartDiscountAmountGroupValidator implements CartDiscountGroupValidatorInterface
{
    /**
     * @var \Pyz\Zed\Product\Persistence\ProductQueryContainer
     */
    protected $productQueryContainer;

    /**
     * @var \Spryker\Zed\Discount\Dependency\Facade\DiscountToMoneyInterface
     */
    protected $moneyFacade;

    /**
     * CartDiscountAmountGroupValidator constructor.
     * @param \Pyz\Zed\Product\Persistence\ProductQueryContainer $productQueryContainer
     * @param \Spryker\Zed\Discount\Dependency\Facade\DiscountToMoneyInterface $moneyFacade
     */
    public function __construct(
        ProductQueryContainer $productQueryContainer,
        DiscountToMoneyInterface $moneyFacade
    )
    {
        $this->productQueryContainer = $productQueryContainer;
        $this->moneyFacade = $moneyFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\CartDiscountGroupValidationTransfer $cartDiscountGroupValidationTransfer
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Discount\Business\Exception\DiscountPriceLowerThanProductPriceException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function isValid(
        CartDiscountGroupValidationTransfer $cartDiscountGroupValidationTransfer
    ): bool
    {
        /* @var $product \Orm\Zed\Product\Persistence\SpyProduct */
        $product = $this
            ->productQueryContainer
            ->queryProduct()
            ->filterBySku(
                $cartDiscountGroupValidationTransfer
                    ->getDiscountSku()
            )
            ->joinWithMerchantPrice()
            ->useMerchantPriceQuery()
                ->filterByFkBranch(
                    $cartDiscountGroupValidationTransfer
                        ->getFkBranch()
                )
            ->endUse()
            ->find()
            ->getFirst();

        /* @var $price \Orm\Zed\MerchantPrice\Persistence\MerchantPrice */
        $price = $product
            ->getMerchantPrices()
            ->getFirst();

        if ($price->getGrossPrice() <= $cartDiscountGroupValidationTransfer->getDiscountPrice()) {
            throw new DiscountPriceLowerThanProductPriceException(
                sprintf(
                    DiscountPriceLowerThanProductPriceException::MESSAGE,
                    $this
                        ->getMoneyString(
                            $cartDiscountGroupValidationTransfer
                                ->getDiscountPrice()
                        ),
                    $cartDiscountGroupValidationTransfer
                        ->getDiscountSku(),
                    $this
                        ->getMoneyString(
                            $price
                                ->getGrossPrice()
                        )
                )
            );
        }

        return true;
    }

    /**
     * @param int $amount
     * @return string
     */
    protected function getMoneyString(int $amount): string
    {
        $moneyTransfer = $this
            ->moneyFacade
            ->fromInteger(
                $amount
            );

        return $this
            ->moneyFacade
            ->formatWithSymbol(
                $moneyTransfer
            );
    }
}
