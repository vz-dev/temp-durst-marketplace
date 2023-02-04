<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 17.01.18
 * Time: 15:14
 */

namespace Pyz\Zed\MerchantPrice\Business\Exception;


class ProductNotFoundException extends \Exception
{
    const NOT_FOUND = 'The product with the id %d could not be found, therefore you cannot add a price for this product';
}