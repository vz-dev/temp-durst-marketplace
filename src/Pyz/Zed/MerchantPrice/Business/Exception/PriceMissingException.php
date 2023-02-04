<?php
/**
 * Durst - project - PriceMissingException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 27.04.18
 * Time: 11:40
 */

namespace Pyz\Zed\MerchantPrice\Business\Exception;


class PriceMissingException extends \Exception
{
    const MESSAGE = 'The price for the product with the sku %s and the branch with the id %d is missing';
}