<?php

namespace Pyz\Zed\MerchantPrice\Business\Model;

use Generated\Shared\Transfer\PriceTransfer;
use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;
use Pyz\Shared\MerchantPrice\MerchantPriceConstants;
use Pyz\Zed\Campaign\Business\Exception\ProductUsedInCampaignException;
use Pyz\Zed\MerchantPrice\Business\Exception\PriceNotFoundException;
use Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculatorInterface;
use Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface;
use Spryker\Zed\Product\Business\ProductFacadeInterface;

class Price
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
     * @var int
     */
    protected $currentBranchId;

    /**
     * @var \Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculatorInterface
     */
    protected $taxAmountCalculator;

    /**
     * @var \Pyz\Zed\MerchantPrice\Communication\Plugin\PostMerchantPriceSavePluginInterface[]
     */
    protected $savePlugins;

    /**
     * @var \Pyz\Zed\MerchantPrice\Communication\Plugin\PostMerchantPriceDeletePluginInterface[]
     */
    protected $deletePlugins;

    public const MESSAGE_ERROR_PRODUCT_USED_IN_CAMPAIGN = 'Das Produkt wird in aktuellen oder zukünftigen Aktionen oder Kampagnen angeboten und kann nicht gelöscht werden!';

    /**
     * Price constructor.
     *
     * @param \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\Product\Business\ProductFacadeInterface $productFacade
     * @param int $currentBranchId
     * @param \Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculatorInterface $taxAmountCalculator
     * @param \Pyz\Zed\MerchantPrice\Communication\Plugin\PostMerchantPriceSavePluginInterface[] $savePlugins
     * @param \Pyz\Zed\MerchantPrice\Communication\Plugin\PostMerchantPriceDeletePluginInterface[] $deletePlugins
     */
    public function __construct(
        MerchantPriceQueryContainerInterface $queryContainer,
        ProductFacadeInterface $productFacade,
        int $currentBranchId,
        TaxAmountCalculatorInterface $taxAmountCalculator,
        array $savePlugins,
        array $deletePlugins
    ) {
        $this->queryContainer = $queryContainer;
        $this->productFacade = $productFacade;
        $this->currentBranchId = $currentBranchId;
        $this->taxAmountCalculator = $taxAmountCalculator;
        $this->savePlugins = $savePlugins;
        $this->deletePlugins = $deletePlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceTransfer $priceTransfer
     *
     * @return \Generated\Shared\Transfer\PriceTransfer | bool
     * be mapped to an existing product in the database
     */
    public function save(PriceTransfer $priceTransfer)
    {
        $priceEntity = $this
            ->queryContainer
            ->queryPriceByIdBranchAndIdProduct(
                $priceTransfer->getFkBranch(),
                $priceTransfer->getFkProduct()
            )
            ->findOneOrCreate();

        $product = $priceEntity->getSpyProduct();
        if ($product === null) {
            return false;
        }

        $productSku = $product->getSku();
        $priceEntity->setSku($productSku . '_' . $priceTransfer->getFkBranch());

        if($this->setPriceModeInEntity($priceEntity, $priceTransfer) === false) {
            return false;
        }

        $this->assertRequirements($priceTransfer);

        $priceEntity->setMerchantSku($priceTransfer->getMerchantSku());
        $priceEntity->setIsActive($priceTransfer->getIsActive());

        $priceEntity->setStatus($priceTransfer->getStatus());

        if ($priceEntity->isNew() || $priceEntity->isModified()) {
            $priceEntity->save();
        }

        $this->runSaverPlugins($priceEntity);

        return $this->entityToTransfer($priceEntity);
    }

    /**
     * Identifies if the price is active or not by the given product sku
     *
     * @param string $sku
     * @return array
     */
    public function getIdBranchForActivePrice(
        string $sku
    ): array {
        $prices = $this
            ->queryContainer
            ->queryPriceBySku($sku)
            ->filterByIsActive(true)
            ->find();

        $branches = [];
        foreach ($prices as $price) {
            $branches[] = $price->getFkBranch();
        }

        return $branches;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceTransfer $priceTransfer
     *
     * @return \Generated\Shared\Transfer\PriceTransfer
     */
    public function import(PriceTransfer $priceTransfer)
    {
        $priceEntity = $this
            ->queryContainer
            ->queryPriceBySku($priceTransfer->getSku())
            ->findOneOrCreate();

        $priceEntity->setFkProduct($priceTransfer->getFkProduct());
        $priceEntity->setFkBranch($priceTransfer->getFkBranch());

        $this->setPriceModeInEntity($priceEntity, $priceTransfer);

        $this->assertRequirements($priceTransfer);

        $priceEntity->setMerchantSku($priceTransfer->getMerchantSku());
        $priceEntity->setIsActive($priceTransfer->getIsActive());
        $priceEntity->setStatus($this->getPriceStatus($priceTransfer->getStatus()));

        if ($priceEntity->isNew() || $priceEntity->isModified()) {
            $priceEntity->save();
        }

        return $this->entityToTransfer($priceEntity);
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
     * @param \Generated\Shared\Transfer\PriceTransfer $transfer
     *
     * @return void
     */
    protected function assertRequirements(PriceTransfer $transfer)
    {
        $transfer->requireMerchantSku()->requireIsActive();
    }

    /**
     * @param \Orm\Zed\MerchantPrice\Persistence\MerchantPrice $priceEntity
     * @param \Generated\Shared\Transfer\PriceTransfer $priceTransfer
     *
     * @return void | bool
     */
    protected function setPriceModeInEntity(MerchantPrice $priceEntity, PriceTransfer $priceTransfer)
    {
        if ($priceTransfer->getPriceMode() !== MerchantPriceConstants::PRICE_MODE_GROSS_NAME
        || $priceTransfer->getGrossPrice() === null
        ) {
            return false;
        }

        $priceEntity->setGrossPrice($priceTransfer->getGrossPrice());
        $priceEntity->setPrice(
            $this->taxAmountCalculator->calculateNetPrice($priceEntity)
        );
    }

    /**
     * @param int $idBranch
     * @param int $idProduct
     *
     * @return bool
     */
    public function hasPriceByIdBranchAndIdProduct($idBranch, $idProduct)
    {
        $amount = $this->queryContainer->queryPriceByIdBranchAndIdProduct($idBranch, $idProduct)->count();

        return $amount > 0;
    }

    /**
     * @param int $idBranch
     * @param int $idProduct
     *
     * @throws \Pyz\Zed\MerchantPrice\Business\Exception\PriceNotFoundException
     *
     * @return \Generated\Shared\Transfer\PriceTransfer
     */
    public function getPriceByIdBranchAndIdProduct($idBranch, $idProduct)
    {
        if ($this->hasPriceByIdBranchAndIdProduct($idBranch, $idProduct) === false) {
            throw new PriceNotFoundException(sprintf(
                PriceNotFoundException::NOT_FOUND,
                $idBranch,
                $idProduct
            ));
        }

        $entity = $this
            ->queryContainer
            ->queryPriceByIdBranchAndIdProduct($idBranch, $idProduct)
            ->findOne();

        return $this->entityToTransfer($entity);
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
        $priceTransfer->setProduct(
            $this->productFacade->findProductConcreteById($priceEntity->getFkProduct())
        );

        return $priceTransfer;
    }

    /**
     * @param $idPrice
     *
     * @throws \Exception
     *
     * @throws \Pyz\Zed\MerchantPrice\Business\Exception\PriceNotFoundException
     */
    public function removePrice($idPrice)
    {
        $price = $this
            ->queryContainer
            ->queryPrices()
            ->filterByIdPrice($idPrice)
            ->filterByFkBranch($this->currentBranchId)
            ->findOne();

        if ($price === null) {
            throw new PriceNotFoundException();
        }

        $priceSku = substr($price->getSku(), 0, strpos($price->getSku(), "_"));
        $priceHasCampaigns = $this
            ->queryContainer
            ->queryProductWithActiveCampaign($priceSku, $this->currentBranchId)
        ->findOne();

        $product = $this->productFacade
            ->findProductConcreteById($price->getFkProduct());

        $priceHasDiscounts = $this
            ->queryContainer
            ->queryProductWithActiveDiscount($product->getSku(), $this->currentBranchId)
            ->findOne();

        if ($priceHasCampaigns !== null
            || $priceHasDiscounts !== null
        ) {
            throw new ProductUsedInCampaignException(ProductUsedInCampaignException::MESSAGE);
        }

        $price->archive();
        $price->delete();

        $this->runDeletePlugins($price);
    }

    /**
     * @param int $idPrice
     *
     * @return bool
     */
    public function hasPriceById($idPrice)
    {
        return ($this
            ->queryContainer
            ->queryPriceById($idPrice)
            ->count() > 0);
    }

    /**
     * @param \Orm\Zed\MerchantPrice\Persistence\MerchantPrice $merchantPrice
     *
     * @return void
     */
    protected function runSaverPlugins(MerchantPrice $merchantPrice)
    {
        foreach ($this->savePlugins as $savePlugin) {
            $savePlugin->postMerchantPriceSave($merchantPrice);
        }
    }

    /**
     * @param \Orm\Zed\MerchantPrice\Persistence\MerchantPrice $merchantPrice
     *
     * @return void
     */
    protected function runDeletePlugins(MerchantPrice $merchantPrice)
    {
        foreach ($this->deletePlugins as $deletePlugin) {
            $deletePlugin->postMerchantPriceDelete($merchantPrice);
        }
    }
}
