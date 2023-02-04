<?php
/**
 * Durst - project - CampaignPrice.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 21.06.21
 * Time: 10:37
 */

namespace Pyz\Zed\MerchantPrice\Business\Model;


use Generated\Shared\Transfer\PriceTransfer;
use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;
use Pyz\Zed\MerchantPrice\Business\Exception\PriceNotFoundException;
use Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculatorInterface;
use Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Zed\Product\Business\ProductFacadeInterface;

class CampaignPrice
{
    /**
     * @var \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Spryker\Zed\Product\Business\ProductFacadeInterface
     */
    protected $productFacade;

    /**
     * @var \Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculatorInterface
     */
    protected $taxAmountCalculator;

    /**
     * CampaignPrice constructor.
     * @param \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\Product\Business\ProductFacadeInterface $productFacade
     * @param \Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculatorInterface $taxAmountCalculator
     */
    public function __construct(
        MerchantPriceQueryContainerInterface $queryContainer,
        ProductFacadeInterface $productFacade,
        TaxAmountCalculatorInterface $taxAmountCalculator
    )
    {
        $this->queryContainer = $queryContainer;
        $this->productFacade = $productFacade;
        $this->taxAmountCalculator = $taxAmountCalculator;
    }

    /**
     * @param int $idBranch
     * @param int $idProduct
     * @return \Generated\Shared\Transfer\PriceTransfer
     * @throws \Pyz\Zed\MerchantPrice\Business\Exception\PriceNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getPriceByIdBranchAndIdProduct(
        int $idBranch,
        int $idProduct
    ): PriceTransfer
    {
        if ($this->hasPriceByIdBranchAndIdProduct($idBranch, $idProduct) === false) {
            throw new PriceNotFoundException(
                sprintf(
                    PriceNotFoundException::NOT_FOUND,
                    $idProduct,
                    $idBranch
                )
            );
        }

        $entity = $this
            ->queryContainer
            ->queryPriceById(
                $idProduct
            )
            ->filterByFkBranch(
                $idBranch
            )
            ->findOne();

        return $this
            ->entityToTransfer(
                $entity
            );
    }

    /**
     * @param int $idBranch
     * @param int $idProduct
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hasPriceByIdBranchAndIdProduct(
        int $idBranch,
        int $idProduct
    ): bool
    {
        $amount = $this
            ->queryContainer
            ->queryPriceById(
                $idProduct
            )
            ->filterByFkBranch(
                $idBranch
            )
            ->count();

        return $amount > 0;
    }

    /**
     * @param \Orm\Zed\MerchantPrice\Persistence\MerchantPrice $priceEntity
     * @return \Generated\Shared\Transfer\PriceTransfer
     */
    protected function entityToTransfer(MerchantPrice $priceEntity): PriceTransfer
    {
        $priceTransfer = new PriceTransfer();

        $priceTransfer
            ->fromArray(
                $priceEntity
                    ->toArray(),
                true
            );

        $priceTransfer->setProduct(
            $this
                ->productFacade
                ->findProductConcreteById(
                    $priceEntity
                        ->getFkProduct()
                )
        );

        return $priceTransfer;
    }
}
