<?php
/**
 * Durst - project - TransitionLog.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 25.02.21
 * Time: 11:58
 */

namespace Pyz\Zed\Oms\Business\Model;


use DateTime;
use DateTimeZone;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\Oms\OmsConfig;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;

class TransitionLog implements TransitionLogInterface
{
    /**
     * @var \Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Oms\OmsConfig
     */
    protected $config;

    /**
     * TransitionLog constructor.
     * @param \Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Oms\OmsConfig $config
     */
    public function __construct(
        OmsQueryContainerInterface $queryContainer,
        OmsConfig $config
    )
    {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @return \DateTime|null
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getDeliveryTimeFromTransitionLogByIdSalesOrder(int $idSalesOrder): ?DateTime
    {
        $transitionLogEntry = $this
            ->queryContainer
            ->queryTransitionLogByIdSalesOrderAndSourceState(
                $idSalesOrder,
                $this
                    ->config
                    ->getWholeSaleAcceptedState()
            )
            ->filterByIsError(false)
            ->_or()
            ->filterByIsError(null, Criteria::ISNULL)
            ->findOne();

        if ($transitionLogEntry !== null) {
            $projectTimezone = new DateTimeZone(
                $this
                    ->config
                    ->getProjectTimeZone()
            );

            return $transitionLogEntry
                ->getCreatedAt()
                ->setTimezone(
                    $projectTimezone
                );
        }

        return null;
    }
}
