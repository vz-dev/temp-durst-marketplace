<?php
/**
 * Durst - project - PriceImport.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.10.20
 * Time: 15:30
 */

namespace Pyz\Zed\MerchantPrice\Business\Model;


use Generated\Shared\Transfer\PriceTransfer;
use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;
use Pyz\Shared\MerchantPrice\MerchantPriceConstants;
use Pyz\Zed\MerchantPrice\Business\Exception\PriceNotFoundException;
use Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculatorInterface;
use Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface;

class PriceImport
{
    /**
     * @var \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculatorInterface
     */
    protected $taxAmountCalculator;

    /**
     * @var \Pyz\Zed\MerchantPrice\Communication\Plugin\PostMerchantPriceDeletePluginInterface[]
     */
    protected $deletePlugins;

    /**
     * PriceImport constructor.
     * @param \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculatorInterface $taxAmountCalculator
     * @param \Pyz\Zed\MerchantPrice\Communication\Plugin\PostMerchantPriceDeletePluginInterface[] $deletePlugins
     */
    public function __construct(
        MerchantPriceQueryContainerInterface $queryContainer,
        TaxAmountCalculatorInterface $taxAmountCalculator,
        array $deletePlugins
    )
    {
        $this->queryContainer = $queryContainer;
        $this->taxAmountCalculator = $taxAmountCalculator;
        $this->deletePlugins = $deletePlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceTransfer $priceTransfer
     * @return \Generated\Shared\Transfer\PriceTransfer | bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function importPriceForBranch(PriceTransfer $priceTransfer)
    {
        $isDeactivated = $this
            ->queryContainer
            ->queryPrices()
            ->useSpyProductQuery()
                ->filterBySku($priceTransfer->getSku())
                ->filterByIsActive(false)
            ->endUse()
            ->count();

        if ($isDeactivated > 0) {
            return false;
        }

        $priceEntity = $this
            ->queryContainer
            ->queryPriceBySku(
                $priceTransfer
                    ->getSku()
            )
            ->findOneOrCreate();

        $priceEntity
            ->setFkProduct(
                $priceTransfer
                    ->getFkProduct()
            );

        $priceEntity
            ->setFkBranch(
                $priceTransfer
                    ->getFkBranch()
            );

        if ($this->setPriceModeInEntity($priceEntity, $priceTransfer) === false) {
            return false;
        }

        $this
            ->assertRequirements($priceTransfer);

        $priceEntity
            ->setMerchantSku(
                $priceTransfer
                    ->getMerchantSku()
            );

        $priceEntity
            ->setIsActive(
                $priceTransfer
                    ->getIsActive()
            );

        $priceEntity
            ->setStatus(
                $this->getPriceStatus(
                    $priceTransfer
                        ->getStatus()
                )
            );

        if ($priceEntity->isNew() || $priceEntity->isModified()) {
            $priceEntity
                ->save();
        }

        return $this
            ->entityToTransfer($priceEntity);
    }

    /**
     * @param $status
     * @return string
     */
    private function getPriceStatus($status): string
    {
        switch ($status) {
            case 0:
                $status = 'inactive';
                break;
            case 1:
                $status = 'active';
                break;
            case 2:
                $status = 'out_of_stock';
                break;
        }

        return $status;
    }

    /**
     * @param int $idPrice
     * @param int $idBranch
     * @return  void | bool
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\MerchantPrice\Business\Exception\PriceNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function removePriceFromBranch(
        int $idPrice,
        int $idBranch
    )
    {
        $price = $this
            ->queryContainer
            ->queryPrices()
            ->filterByIdPrice($idPrice)
            ->filterByFkBranch($idBranch)
            ->findOne();

        if ($price === null) {
            throw new PriceNotFoundException();
        }

        $priceSku = substr($price->getSku(), 0, strpos($price->getSku(), "_"));
        $priceHasCampaigns = $this
            ->queryContainer
            ->queryProductWithActiveCampaign($priceSku, $idBranch)
            ->findOne();

        $priceHasDiscounts = $this
            ->queryContainer
            ->queryProductWithActiveDiscount($priceSku, $idBranch)
            ->findOne();

        if ($priceHasCampaigns !== null
            || $priceHasDiscounts !== null
        ) {
            return false;
        }

        $price->archive();
        $price->delete();

        $this
            ->runDeletePlugins($price);
    }

    /**
     * @param \Orm\Zed\MerchantPrice\Persistence\MerchantPrice $priceEntity
     * @param \Generated\Shared\Transfer\PriceTransfer $priceTransfer
     * @return bool | void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function setPriceModeInEntity(
        MerchantPrice $priceEntity,
        PriceTransfer $priceTransfer
    ) {
        if ($priceTransfer->getPriceMode() !== MerchantPriceConstants::PRICE_MODE_GROSS_NAME
            || $priceTransfer->getGrossPrice() === null
        ) {
            return false;
        }

        $priceEntity
            ->setGrossPrice(
                $priceTransfer
                    ->getGrossPrice()
            );

        $priceEntity
            ->setPrice(
                $this
                    ->taxAmountCalculator
                    ->calculateNetPrice($priceEntity)
            );
    }

    /**
     * @param \Generated\Shared\Transfer\PriceTransfer $transfer
     * @return void
     */
    protected function assertRequirements(PriceTransfer $transfer): void
    {
        $transfer
            ->requireMerchantSku()
            ->requireIsActive();
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

        return $priceTransfer;
    }

    /**
     * @param \Orm\Zed\MerchantPrice\Persistence\MerchantPrice $merchantPrice
     * @return void
     */
    protected function runDeletePlugins(MerchantPrice $merchantPrice): void
    {
        foreach ($this->deletePlugins as $deletePlugin) {
            $deletePlugin->postMerchantPriceDelete($merchantPrice);
        }
    }
}
