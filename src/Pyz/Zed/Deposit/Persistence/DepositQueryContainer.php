<?php

namespace Pyz\Zed\Deposit\Persistence;

use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * @method \Pyz\Zed\Deposit\Persistence\DepositPersistenceFactory getFactory()
 */
class DepositQueryContainer extends AbstractQueryContainer implements DepositQueryContainerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param int $idDeposit
     * @return \Orm\Zed\Deposit\Persistence\SpyDepositQuery
     */
    public function queryDepositByIdDeposit($idDeposit)
    {
        return $this
            ->getFactory()
            ->createDepositQuery()
            ->filterByIdDeposit($idDeposit);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name
     * @return \Orm\Zed\Deposit\Persistence\SpyDepositQuery
     */
    public function queryDepositByName($name)
    {
        return $this
            ->getFactory()
            ->createDepositQuery()
            ->filterByName();
    }

    /**
     * {@inheritdoc}
     *
     * @return \Orm\Zed\Deposit\Persistence\SpyDepositQuery
     */
    public function queryDeposit()
    {
        return $this
            ->getFactory()
            ->createDepositQuery();
    }
}
