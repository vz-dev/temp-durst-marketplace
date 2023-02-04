<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-13
 * Time: 13:01
 */

namespace Pyz\Zed\Discount\Business\Calculator;

use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\Discount\Persistence\SpyDiscount;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\Discount\Business\Calculator\CalculatorInterface;
use Spryker\Zed\Discount\Business\Calculator\Discount as SprykerDiscount;
use Spryker\Zed\Discount\Business\Persistence\DiscountEntityMapperInterface;
use Spryker\Zed\Discount\Business\QueryString\SpecificationBuilderInterface;
use Spryker\Zed\Discount\Business\Voucher\VoucherValidatorInterface;
use Spryker\Zed\Discount\Dependency\Plugin\DiscountApplicableFilterPluginInterface;

class Discount extends SprykerDiscount implements DiscountInterface
{
    /**
     * @var DiscountQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var CalculatorInterface
     */
    protected $calculator;

    /**
     * @var SpecificationBuilderInterface
     */
    protected $decisionRuleBuilder;

    /**
     * @var VoucherValidatorInterface
     */
    protected $voucherValidator;

    /**
     * @var DiscountApplicableFilterPluginInterface[]
     */
    protected $discountApplicableFilterPlugins = [];

    /**
     * @var DiscountEntityMapperInterface
     */
    protected $discountEntityMapper;

    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var int
     */
    protected $idBranch;

    /**
     * Discount constructor.
     * @param DiscountQueryContainerInterface $queryContainer
     * @param CalculatorInterface $calculator
     * @param SpecificationBuilderInterface $decisionRuleBuilder
     * @param VoucherValidatorInterface $voucherValidator
     * @param DiscountEntityMapperInterface $discountEntityMapper
     * @param MerchantFacadeInterface $merchantFacade
     */
    public function __construct(
        DiscountQueryContainerInterface $queryContainer,
        CalculatorInterface $calculator,
        SpecificationBuilderInterface $decisionRuleBuilder,
        VoucherValidatorInterface $voucherValidator,
        DiscountEntityMapperInterface $discountEntityMapper,
        MerchantFacadeInterface $merchantFacade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->calculator = $calculator;
        $this->decisionRuleBuilder = $decisionRuleBuilder;
        $this->voucherValidator = $voucherValidator;
        $this->discountEntityMapper = $discountEntityMapper;
        $this->merchantFacade = $merchantFacade;

        parent::__construct(
            $queryContainer,
            $calculator,
            $decisionRuleBuilder,
            $voucherValidator,
            $discountEntityMapper
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @return QuoteTransfer
     */
    public function calculate(QuoteTransfer $quoteTransfer)
    {
        $this->idBranch = $quoteTransfer
            ->getFkBranch();

        return parent::calculate($quoteTransfer);
    }

    /**
     * @param array $voucherCodes
     * @return SpyDiscount[]|\Propel\Runtime\Collection\ObjectCollection
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function retrieveActiveCartAndVoucherDiscounts(array $voucherCodes = [])
    {
        $discounts = $this
            ->queryContainer
            ->queryActiveCartRules()
            ->filterByFkBranch($this->idBranch)
            ->find();

        if (count($voucherCodes) > 0) {
            $voucherDiscounts = $this
                ->queryContainer
                ->queryDiscountsBySpecifiedVouchers($voucherCodes)
                ->find();

            $voucherDiscounts = $this
                ->filterUniqueVoucherDiscounts($voucherDiscounts);

            if (count($discounts) == 0) {
                return $voucherDiscounts;
            }

            foreach ($voucherDiscounts as $discountEntity) {
                $discounts->append($discountEntity);
            }
        }

        return $discounts;
    }
}
