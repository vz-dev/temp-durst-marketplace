<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Orm\Zed\Sales\Persistence;

use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Sales\Persistence\Propel\AbstractSpySalesOrder as BaseSpySalesOrder;

/**
 * Skeleton subclass for representing a row from the 'spy_sales_order' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class SpySalesOrder extends BaseSpySalesOrder
{
    /**
     * @return bool
     */
    public function hasDiscount(): bool
    {
        return $this->collDiscounts !== null && $this->collDiscounts->count() > 0;
    }

    /**
     * @return SpySalesDiscount[]|ObjectCollection
     */
    public function getCachedDiscounts(): ObjectCollection
    {
        return $this->collDiscounts;
    }
}
