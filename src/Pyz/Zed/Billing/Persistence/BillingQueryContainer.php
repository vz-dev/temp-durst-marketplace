<?php

namespace Pyz\Zed\Billing\Persistence;

use DateTime;
use Orm\Zed\Billing\Persistence\DstBillingItemQuery;
use Orm\Zed\Billing\Persistence\DstBillingPeriodQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * @method \Pyz\Zed\Billing\Persistence\BillingPersistenceFactory getFactory()
 */
class BillingQueryContainer extends AbstractQueryContainer implements BillingQueryContainerInterface
{
    /**
     * {@inheritdoc}
     */
    public function queryBillingPeriod() : DstBillingPeriodQuery
    {
        return $this
            ->getFactory()
            ->createBillingPeriodQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $endDate
     *
     * @return \Orm\Zed\Billing\Persistence\DstBillingPeriodQuery
     */
    public function queryBillingPeriodGetByEndDate(string $endDate) : DstBillingPeriodQuery
    {
        return $this
            ->getFactory()
            ->createBillingPeriodQuery()
            ->filterByEndDate($endDate);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Orm\Zed\Billing\Persistence\DstBillingItemQuery
     */
    public function queryBillingItem() : DstBillingItemQuery
    {
        return $this
            ->getFactory()
            ->createBillingItemQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $fkBranch
     *
     * @return \Orm\Zed\Billing\Persistence\DstBillingPeriodQuery
     */
    public function queryClosedBillingPeriodsByFkBranch(int $fkBranch): DstBillingPeriodQuery
    {
        return $this
            ->queryBillingPeriod()
            ->filterByFkBranch($fkBranch)
            ->filterByEndDate(
                (new DateTime('now'))->format(DateTime::ATOM),
                Criteria::LESS_THAN
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \DateTime $time
     * @param int $fkBranch
     *
     * @return \Orm\Zed\Billing\Persistence\DstBillingPeriodQuery
     */
    public function queryBillingPeriodByTimeAndBranch(DateTime $time, int $fkBranch): DstBillingPeriodQuery
    {
        return $this
            ->queryBillingPeriod()
            ->filterByStartDate($time->format(DateTime::ATOM), Criteria::LESS_EQUAL)
            ->filterByEndDate(date($time->format(DateTime::ATOM)), Criteria::GREATER_EQUAL)
            ->filterByFkBranch($fkBranch);
    }

    /**
     * @param int $idBillingPeriod
     *
     * @return \Orm\Zed\Billing\Persistence\DstBillingPeriodQuery
     */
    public function queryInvoiceInformationForBillingPeriodById(int $idBillingPeriod): DstBillingPeriodQuery
    {
        return $this
            ->getFactory()
            ->createBillingPeriodQuery()
            ->filterByIdBillingPeriod($idBillingPeriod)
            ->groupByBillingReference()
            ->useDstBillingItemQuery()
                ->useSpySalesOrderQuery()
                    ->filterByInvoiceCreatedAt(null, Criteria::ISNOTNULL)
                    ->useItemQuery()
                        ->useProcessQuery()
                        ->endUse()
                        ->useStateQuery()
                        ->endUse()
                    ->endUse()
                    ->useSpyConcreteTimeSlotQuery()
                        ->useDstConcreteTourQuery()
                        ->endUse()
                    ->endUse()
                    ->useExpenseQuery()
                    ->endUse()
                    ->useDstPaymentHeidelpayRestLogQuery(null, Criteria::LEFT_JOIN)
                    ->endUse()
                    ->useShippingAddressQuery()
                    ->endUse()
                    ->useOrderQuery() // useSpySalesPaymentQuery() is wrongly named useOrderQuery()
                        ->useSalesPaymentMethodTypeQuery()
                        ->endUse()
                    ->endUse()
                ->endUse()
            ->endUse();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return \Orm\Zed\Billing\Persistence\DstBillingPeriodQuery
     */
    public function queryLatestBillingPeriodForBranch(int $idBranch): DstBillingPeriodQuery
    {
        return $this
            ->queryBillingPeriod()
            ->filterByFkBranch($idBranch)
            ->orderByEndDate(Criteria::DESC)
            ->limit(1);
    }
}
