<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-19
 * Time: 12:32
 */

namespace Pyz\Zed\Discount\Communication\Controller;


use ArrayObject;
use Exception;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\DiscountApiRequestTransfer;
use Generated\Shared\Transfer\DiscountApiResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * Class GatewayController
 * @package Pyz\Zed\Discount\Business\Collector
 * @method DiscountFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    protected const DISCOUNT_VOUCHER_CALCULATION_ERROR = 'Bei der Berechnung des Gutscheines "%s" gab es einen Fehler.';
    protected const DISCOUNT_VOUCHER_CODE_INVALID = 'Leider ist der Gutschein "%s" ungültig.';

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getActiveDiscountsForBranchesAction(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        $branchIds = $requestTransfer
            ->getBranchIds();

        $discountsArray = $this
            ->getFacade()
            ->getActiveDiscountsByBranches($branchIds);

        $discounts = new \ArrayObject($discountsArray);

        return (new AppApiResponseTransfer())
            ->setDiscounts($discounts);
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getActiveDiscountsForProductAction(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        $idBranch = $requestTransfer->getIdBranch();
        $sku = $requestTransfer->getSku();

        $discountsArray = $this
            ->getFacade()
            ->getActiveDiscountsForProduct($idBranch, $sku);

        $discounts = new ArrayObject($discountsArray);

        return (new AppApiResponseTransfer())
            ->setDiscounts($discounts);
    }

    /**
     * @param \Generated\Shared\Transfer\DiscountApiRequestTransfer $discountApiRequestTransfer
     * @return \Generated\Shared\Transfer\DiscountApiResponseTransfer
     */
    public function checkValidVoucherAction(DiscountApiRequestTransfer $discountApiRequestTransfer): DiscountApiResponseTransfer
    {
        $response = new DiscountApiResponseTransfer();

        try {
            $quote = $this
                ->getFacade()
                ->addVoucherCodeToQuote(
                    $discountApiRequestTransfer
                );
        } catch (Exception $exception) {
            return $response
                ->setValid(false)
                ->setErrorMessage(
                    sprintf(
                        static::DISCOUNT_VOUCHER_CALCULATION_ERROR,
                        $discountApiRequestTransfer
                            ->getVoucherCode()
                    )
                );
        }

        if ($this->isVoucherCodeApplied($quote, $discountApiRequestTransfer->getVoucherCode()) === true) {
            return $response
                ->setValid(true);
        }

        return $response
            ->setValid(false)
            ->setErrorMessage(
                sprintf(
                    static::DISCOUNT_VOUCHER_CODE_INVALID,
                    $discountApiRequestTransfer
                        ->getVoucherCode()
                )
            );
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $voucherCode
     * @return bool
     */
    protected function isVoucherCodeApplied(
        QuoteTransfer $quoteTransfer,
        string $voucherCode
    ): bool
    {
        foreach ($quoteTransfer->getVoucherDiscounts() as $voucherDiscount) {
            if ($voucherDiscount->getVoucherCode() === $voucherCode) {
                return true;
            }
        }

        return false;
    }
}
