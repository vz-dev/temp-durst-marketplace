<?php
/**
 * Created by PhpStorm.
 * User: ikesimmons
 * Date: 20.06.18
 * Time: 11:07
 */

namespace Pyz\Zed\Product\Business\Hydrator;


use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Product\Persistence\ProductQueryContainer;

class ProductNameHydrator
{
    const KEY_ATTR_NAME = 'name';
    const KEY_ATTR_UNIT = 'unit';


    /**
     * @var ProductQueryContainer
     */
    protected $productQueryContainer;

    public function __construct($productQueryContainer)
    {
        $this->productQueryContainer =$productQueryContainer;
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateProductName(OrderTransfer $orderTransfer): OrderTransfer
    {
        foreach ($orderTransfer->getItems() as $item)
        {
            $name = explode(' - ', $item->getName());
            $item->setProductName($name[0]);
            $unitName = '';
            if(count($name) !== 1){
                $unitName = $name[1];
            }
            $item->setUnitName($unitName);
            continue;
            $product = $this->productQueryContainer->queryProduct()->findOneByIdProduct($item->getId());
            $productAttrs = json_decode($product->getAttributes());

            /**
             * if we have a product name and unit name in product attrs, set the name as a combo of the two values,
             * otherwise it is left as the sku
             */
            if (isset($productAttrs->{self::KEY_ATTR_NAME}) && isset($productAttrs->{self::KEY_ATTR_UNIT}))
            {
                $item->setName(
                    sprintf('%s %s',
                        $productAttrs->{self::KEY_ATTR_NAME},
                        $productAttrs->{self::KEY_ATTR_UNIT}
                    )
                );
                $item->setProductName($productAttrs->{self::KEY_ATTR_NAME});
                $item->setUnitName($productAttrs->{self::KEY_ATTR_UNIT});
            }
        }

        return $orderTransfer;
    }

}
