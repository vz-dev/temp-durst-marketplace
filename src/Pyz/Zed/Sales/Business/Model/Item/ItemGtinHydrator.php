<?php


namespace Pyz\Zed\Sales\Business\Model\Item;


use ArrayObject;
use Generated\Shared\Transfer\GtinTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Product\Persistence\SpyProduct;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Product\Persistence\ProductQueryContainerInterface;
use stdClass;

class ItemGtinHydrator implements ItemGtinHydratorInterface
{
    protected const KEY_ATTR_NAME = 'name';
    protected const KEY_ATTR_UNIT = 'unit';
    protected const KEY_ATTR_GTIN = 'gtin';

    /**
     * @var ProductQueryContainerInterface
     */
    protected $productQueryContainer;

    /**
     * ItemGtinHydrator constructor.
     * @param ProductQueryContainerInterface $productQueryContainer
     */
    public function __construct(ProductQueryContainerInterface $productQueryContainer)
    {
        $this->productQueryContainer = $productQueryContainer;
    }

    /**
     * @inheritDoc
     */
    public function hydrateItemGtin(OrderTransfer $orderTransfer): OrderTransfer
    {
        foreach ($orderTransfer->getItems() as $item) {
            $product = $this
                ->productQueryContainer
                ->queryProduct()
                ->findOneByIdProduct($item->getId());

            if($product !== null){
                $gtins = $this
                    ->createGtinTransfers($product);

                $item->setGtins($gtins);
            }
        }

        return $orderTransfer;
    }

    /**
     * @param SpyProduct $product
     * @return ArrayObject
     * @throws PropelException
     */
    protected function createGtinTransfers(SpyProduct $product): ArrayObject
    {
        $productAttributes = $this
            ->combineAttributes($product);

        $gtins = new ArrayObject();

        if (isset($productAttributes->{self::KEY_ATTR_GTIN})) {
            $productName = '';
            $unit = '';
            $sku = $product->getSku();

            if (isset($productAttributes->{self::KEY_ATTR_NAME})) {
                $productName = $productAttributes->{self::KEY_ATTR_NAME};
            }

            if (isset($productAttributes->{self::KEY_ATTR_UNIT})) {
                $unit = $productAttributes->{self::KEY_ATTR_UNIT};
            }

            foreach ($productAttributes->{self::KEY_ATTR_GTIN} as $currentGtin) {
                $gtin = new GtinTransfer();
                $gtin
                    ->setGtin($currentGtin)
                    ->setProductName($productName)
                    ->setSku($sku)
                    ->setUnit($unit);

                $gtins->append($gtin);
            }
        }

        return $gtins;
    }

    /**
     * @param SpyProduct $product
     * @return stdClass
     * @throws PropelException
     */
    protected function combineAttributes(SpyProduct $product): stdClass
    {
        $localizedAttributes = $product
            ->getSpyProductLocalizedAttributess();

        $productAttributes = json_decode($product->getAttributes());

        $gtins = [];

        foreach ($localizedAttributes as $localizedAttribute) {
            $allAttributes = json_decode($localizedAttribute->getAttributes());

            if (isset($allAttributes->{self::KEY_ATTR_GTIN})) {
                $gtins[] = $allAttributes->{self::KEY_ATTR_GTIN};
            }
        }

        $combinedAttributes = new stdClass();

        $combinedAttributes->{self::KEY_ATTR_GTIN} = $gtins;
        $combinedAttributes->{self::KEY_ATTR_NAME} = $productAttributes->{self::KEY_ATTR_NAME};
        $combinedAttributes->{self::KEY_ATTR_UNIT} = $productAttributes->{self::KEY_ATTR_UNIT};

        return $combinedAttributes;
    }
}
