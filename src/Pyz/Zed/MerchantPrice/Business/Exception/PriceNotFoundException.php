<?php

namespace Pyz\Zed\MerchantPrice\Business\Exception;

use Exception;

class PriceNotFoundException extends Exception
{
    const NOT_FOUND = 'The price for the product with the id %d and the branch with id %d can not be found';
}
