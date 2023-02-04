<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-18
 * Time: 12:56
 */

namespace Pyz\Zed\Refund\Business;

use Pyz\Zed\Refund\Business\Model\RefundReader;
use Pyz\Zed\Refund\Business\Model\RefundReaderInterface;
use Pyz\Zed\Refund\Business\Model\RefundSaver;
use Pyz\Zed\Refund\Business\Model\ReturnItemReader;
use Pyz\Zed\Refund\Business\Model\ReturnItemReaderInterface;
use Pyz\Zed\Refund\Persistence\RefundQueryContainerInterface;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Spryker\Zed\Refund\Business\Model\RefundSaverInterface;
use Spryker\Zed\Refund\Business\RefundBusinessFactory as SprykerRefundBusinessFactory;

/**
 * Class RefundBusinessFactory
 * @package Pyz\Zed\Refund\Business
 * @method RefundQueryContainerInterface getQueryContainer()
 * @method SalesQueryContainerInterface getSalesQueryContainer()
 */
class RefundBusinessFactory extends SprykerRefundBusinessFactory
{

    /**
     * @return RefundReaderInterface
     */
    public function createRefundReader(): RefundReaderInterface
    {
        return new RefundReader(
            $this->getQueryContainer()
        );
    }

    /**
     * @return ReturnItemReaderInterface
     */
    public function createReturnItemReader(): ReturnItemReaderInterface
    {
        return new ReturnItemReader(
            $this->getQueryContainer(),
            $this->getSalesQueryContainer()
        );
    }

    /**
     * @return \Spryker\Zed\Refund\Business\Model\RefundSaverInterface
     */
    public function createRefundSaver(): RefundSaverInterface
    {
        return new RefundSaver(
            $this->getSalesQueryContainer(),
            $this->getSalesFacade(),
            $this->getCalculationFacade()
        );
    }
}
