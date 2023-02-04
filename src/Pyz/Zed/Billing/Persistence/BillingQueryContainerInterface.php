<?php

namespace Pyz\Zed\Billing\Persistence;

use DateTime;
use Orm\Zed\Billing\Persistence\DstBillingItemQuery;
use Orm\Zed\Billing\Persistence\DstBillingPeriodQuery;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

interface BillingQueryContainerInterface extends QueryContainerInterface
{
    /**
     * @return \Orm\Zed\Billing\Persistence\DstBillingPeriodQuery
     */
    public function queryBillingPeriod() : DstBillingPeriodQuery;

    /**
     * @param string $endDate
     *
     * @return \Orm\Zed\Billing\Persistence\DstBillingPeriodQuery
     */
    public function queryBillingPeriodGetByEndDate(string $endDate) : DstBillingPeriodQuery;

    /**
     * @return \Orm\Zed\Billing\Persistence\DstBillingItemQuery
     */
    public function queryBillingItem() : DstBillingItemQuery;

    /**
     * @param int $fkBranch
     *
     * @return \Orm\Zed\Billing\Persistence\DstBillingPeriodQuery
     */
    public function queryClosedBillingPeriodsByFkBranch(int $fkBranch): DstBillingPeriodQuery;

    /**
     * @param int $idBillingPeriod
     *
     * @return \Orm\Zed\Billing\Persistence\DstBillingPeriodQuery
     */
    public function queryInvoiceInformationForBillingPeriodById(int $idBillingPeriod): DstBillingPeriodQuery;

    /**
     * @param \DateTime $time
     * @param int $fkBranch
     *
     * @return \Orm\Zed\Billing\Persistence\DstBillingPeriodQuery
     */
    public function queryBillingPeriodByTimeAndBranch(DateTime $time, int $fkBranch): DstBillingPeriodQuery;

    /**
     * @param int $idBranch
     *
     * @return \Orm\Zed\Billing\Persistence\DstBillingPeriodQuery
     */
    public function queryLatestBillingPeriodForBranch(int $idBranch): DstBillingPeriodQuery;
}
