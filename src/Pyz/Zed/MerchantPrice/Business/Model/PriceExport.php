<?php
/**
 * Durst - project - BranchPrice.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.09.20
 * Time: 15:32
 */

namespace Pyz\Zed\MerchantPrice\Business\Model;


use Generated\Shared\Transfer\PriceTransfer;
use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;
use Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface;
use Pyz\Zed\Product\Business\ProductFacadeInterface;

class PriceExport
{
    /**
     * @var \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Product\Business\ProductFacadeInterface
     */
    protected $productFacade;

    /**
     * PriceExport constructor.
     * @param \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Product\Business\ProductFacadeInterface $productFacade
     */
    public function __construct(
        MerchantPriceQueryContainerInterface $queryContainer,
        ProductFacadeInterface $productFacade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->productFacade = $productFacade;
    }

    /**
     * @param int $idBranch
     * @return PriceTransfer[]
     */
    public function getPricesForBranch(int $idBranch): array
    {
        $priceEntities = $this
            ->queryContainer
            ->queryPricesByIdBranch(
                $idBranch
            )->find();

        $prices = [];

        foreach ($priceEntities as $priceEntity) {
            $prices[] = $this
                ->entityToTransfer(
                    $priceEntity
                );
        }

        return $prices;
    }

    /**
     * @param \Orm\Zed\MerchantPrice\Persistence\MerchantPrice $priceEntity
     *
     * @return \Generated\Shared\Transfer\PriceTransfer
     */
    protected function entityToTransfer(MerchantPrice $priceEntity)
    {
        $priceTransfer = new PriceTransfer();
        $priceTransfer->fromArray($priceEntity->toArray(), true);

        return $priceTransfer;
    }
}
