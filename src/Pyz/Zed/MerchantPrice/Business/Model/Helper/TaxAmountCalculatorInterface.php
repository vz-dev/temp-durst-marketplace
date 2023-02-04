<?php
/**
 * Durst - project - TaxAmountCalculatorInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 20.07.18
 * Time: 10:52
 */

namespace Pyz\Zed\MerchantPrice\Business\Model\Helper;


use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;

interface TaxAmountCalculatorInterface
{
    /**
     * @param MerchantPrice $entity
     * @return int
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function calculateGrossPrice(MerchantPrice $entity) : int;

    /**
     * @param MerchantPrice $entity
     * @return int
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function calculateNetPrice(MerchantPrice $entity) : int;
}