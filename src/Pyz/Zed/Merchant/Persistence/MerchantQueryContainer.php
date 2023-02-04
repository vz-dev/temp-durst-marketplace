<?php
/**
 * Durst - project - MerchantQueryContainer.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 10:56
 */

namespace Pyz\Zed\Merchant\Persistence;

use Orm\Zed\Deposit\Persistence\Map\SpyDepositTableMap;
use Orm\Zed\Merchant\Persistence\DstBranchToDepositQuery;
use Orm\Zed\Merchant\Persistence\DstBranchUserQuery;
use Orm\Zed\Merchant\Persistence\DstMerchantUserQuery;
use Orm\Zed\Merchant\Persistence\Map\DstBranchToDepositTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchToPaymentMethodTableMap;
use Orm\Zed\Merchant\Persistence\SpyBranchQuery;
use Orm\Zed\Merchant\Persistence\SpyBranchToPaymentMethodQuery;
use Orm\Zed\Merchant\Persistence\SpyEnumSalutationQuery;
use Orm\Zed\Merchant\Persistence\SpyMerchantQuery;
use Orm\Zed\Merchant\Persistence\SpyPaymentMethodQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

class MerchantQueryContainer extends AbstractQueryContainer implements MerchantQueryContainerInterface
{
    public const COND_FK_BRANCH = 'COND_FK_BRANCH';
    public const COND_FK_BRANCH_IS_NULL = 'COND_FK_BRANCH_IS_NULL';
    public const COND_DEPOSIT_GT_ZERO = 'COND_DEPOSIT_GT_ZERO';

    public const COMB_BRANCH = 'COMB_BRANCH';

    /**
     * {@inheritdoc}
     *
     * @param string $merchantname
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryMerchantByMerchantname(string $merchantname): SpyMerchantQuery
    {
        $query = $this->getFactory()->createMerchantQuery();
        $query->filterByMerchantname($merchantname);

        return $query;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $id
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryMerchantById(int $id): SpyMerchantQuery
    {
        return $this
            ->queryMerchant()
            ->filterByIdMerchant($id);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     */
    public function queryMerchant(): SpyMerchantQuery
    {
        return $this
            ->getFactory()
            ->createMerchantQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @return \Orm\Zed\Merchant\Persistence\SpyBranchQuery
     */
    public function queryBranch(): SpyBranchQuery
    {
        return $this
            ->getFactory()
            ->createBranchQuery();
    }

    /**
     * @return SpyBranchQuery
     */
    public function queryBranchNotDeleted(): SpyBranchQuery
    {
        return $this
            ->queryBranch()
            ->filterByStatus_In([
                SpyBranchTableMap::COL_STATUS_ACTIVE,
                SpyBranchTableMap::COL_STATUS_BLOCKED
            ]);
    }

    /**
     * @return SpyBranchQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryBranchActive(): SpyBranchQuery
    {
        return $this
            ->queryBranch()
            ->filterByStatus(SpyBranchTableMap::COL_STATUS_ACTIVE);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @return \Orm\Zed\Merchant\Persistence\SpyBranchQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryBranchByIdMerchant(int $idMerchant): SpyBranchQuery
    {
        return $this
            ->queryBranchNotDeleted()
            ->filterByFkMerchant($idMerchant);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $id
     * @return \Orm\Zed\Merchant\Persistence\SpyBranchQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryBranchById(int $id): SpyBranchQuery
    {
        return $this
            ->queryBranch()
            ->filterByIdBranch($id);
    }

    /**
     * @param string $name
     * @return \Orm\Zed\Merchant\Persistence\SpyBranchQuery
     */
    public function queryBranchByName(string $name): SpyBranchQuery
    {
        return $this
            ->getFactory()
            ->createBranchQuery()
            ->filterByName($name);
    }

    /**
     * {@inheritdoc}
     *
     * @return SpyBranchToPaymentMethodQuery
     */
    public function queryBranchToPaymentMethod(): SpyBranchToPaymentMethodQuery
    {
        return $this
            ->getFactory()
            ->createBranchToPaymentMethodQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @return SpyPaymentMethodQuery
     */
    public function queryPaymentMethod(): SpyPaymentMethodQuery
    {
        return $this
            ->getFactory()
            ->createPaymentMethodQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @return \Orm\Zed\Merchant\Persistence\SpyEnumSalutationQuery
     */
    public function queryEnumSalutation(): SpyEnumSalutationQuery
    {
        return $this
            ->getFactory()
            ->createEnumSalutationQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @return SpyBranchQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryBranchByCode(string $code): SpyBranchQuery
    {
        return $this
            ->queryBranch()
            ->filterByCode($code);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     * @return SpyPaymentMethodQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryPossiblePaymentMethodsForMerchant(int $idMerchant): SpyPaymentMethodQuery
    {
        //ToDo fix dependency to SoftwarePackage
        return $this
            ->queryPaymentMethod()
            ->leftJoinWithSpyBranchToPaymentMethod()
            ->withColumn(SpyBranchToPaymentMethodTableMap::COL_B2B, 'b2b')
            ->withColumn(SpyBranchToPaymentMethodTableMap::COL_B2C, 'b2c')
            ->useDstSoftwarePackageToPaymentMethodQuery()
            ->useDstSoftwarePackageQuery()
            ->useSpyMerchantQuery()
            ->filterByIdMerchant($idMerchant)
            ->endUse()
            ->endUse()
            ->endUse();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     * @return SpyPaymentMethodQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryPossiblePaymentMethodsForMerchantByBranchId(int $idMerchant, int $idBranch): SpyPaymentMethodQuery
    {
        //ToDo fix dependency to SoftwarePackage
        return $this
            ->queryPaymentMethod()
            ->useSpyBranchToPaymentMethodQuery()
            ->filterByFkBranch($idBranch)
            ->withColumn(SpyBranchToPaymentMethodTableMap::COL_B2B, 'b2b')
            ->withColumn(SpyBranchToPaymentMethodTableMap::COL_B2C, 'b2c')
            ->endUse()
            ->useDstSoftwarePackageToPaymentMethodQuery()
            ->useDstSoftwarePackageQuery()
            ->useSpyMerchantQuery()
            ->filterByIdMerchant($idMerchant)
            ->endUse()
            ->endUse()
            ->endUse();
    }

    /**
     * @param string $code
     * @return SpyPaymentMethodQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryPaymentMethodByCode(string $code): SpyPaymentMethodQuery
    {
        return $this
            ->queryPaymentMethod()
            ->filterByCode($code);
    }

    /**
     * @param int $idBranch
     *
     * @return \Orm\Zed\Merchant\Persistence\DstBranchToDepositQuery
     */
    public function queryBranchToDepositWithDeposits(int $idBranch): DstBranchToDepositQuery
    {
        return $this
            ->queryBranchToDeposit()
            ->joinWithSpyDeposit(Criteria::RIGHT_JOIN)
            ->condition(
                static::COND_FK_BRANCH,
                DstBranchToDepositTableMap::COL_FK_BRANCH . '= ?',
                $idBranch
            )
            ->condition(
                static::COND_FK_BRANCH_IS_NULL,
                DstBranchToDepositTableMap::COL_FK_BRANCH . Criteria::ISNULL
            )
            ->condition(
                static::COND_DEPOSIT_GT_ZERO,
                SpyDepositTableMap::COL_DEPOSIT . '> ?',
                0
            )
            ->combine(
                [
                    static::COND_FK_BRANCH,
                    static::COND_FK_BRANCH_IS_NULL,
                ],
                'or',
                static::COMB_BRANCH
            )
            ->where(
                [
                    static::COMB_BRANCH,
                    static::COND_DEPOSIT_GT_ZERO,
                ],
                'and'
            )
            ->orderBy(
                SpyDepositTableMap::COL_PRESENTATION_NAME
            );
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return \Orm\Zed\Merchant\Persistence\DstBranchToDepositQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryBranchToDepositByIdBranch(int $idBranch): DstBranchToDepositQuery
    {
        return $this
            ->queryBranchToDeposit()
            ->filterByFkBranch($idBranch);
    }

    /**
     * @return \Orm\Zed\Merchant\Persistence\DstBranchToDepositQuery
     */
    public function queryBranchToDeposit(): DstBranchToDepositQuery
    {
        return $this
            ->getFactory()
            ->createBranchToDepositQuery();
    }

    /**
     * @param int $idBranch
     *
     * @return \Orm\Zed\Merchant\Persistence\DstBranchToDepositQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryBranchToDepositWithAcceptedDeposits(int $idBranch): DstBranchToDepositQuery
    {
        return $this
            ->queryBranchToDeposit()
            ->useSpyDepositQuery()
            ->filterByDeposit(0, Criteria::GREATER_THAN)
            ->orderBy(
                SpyDepositTableMap::COL_PRESENTATION_NAME
            )
            ->endUse()
            ->filterByFkBranch($idBranch);
    }

    /**
     * @param string $merchantPin
     * @return SpyMerchantQuery
     */
    public function queryMerchantByMerchantPin(string $merchantPin): SpyMerchantQuery
    {
        return $this
            ->getFactory()
            ->createMerchantQuery()
            ->filterByMerchantPin($merchantPin);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\Merchant\Persistence\DstBranchUserQuery
     */
    public function queryBranchUser(): DstBranchUserQuery
    {
        return $this
            ->getFactory()
            ->createBranchUserQuery();
    }

    /**
     * {@inheritDoc}
     *
     * @param int|null $idBranchUser
     * @return \Orm\Zed\Merchant\Persistence\DstBranchUserQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryBranchUserById(?int $idBranchUser): DstBranchUserQuery
    {
        return $this
            ->queryBranchUser()
            ->filterByIdBranchUser($idBranchUser);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $email
     * @return \Orm\Zed\Merchant\Persistence\DstBranchUserQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryBranchUserByEmail(string $email): DstBranchUserQuery
    {
        return $this
            ->queryBranchUser()
            ->filterByEmail($email);
    }

    /**
     * @return \Orm\Zed\Merchant\Persistence\DstMerchantUserQuery
     */
    public function queryMerchantUser(): DstMerchantUserQuery
    {
        return $this
            ->getFactory()
            ->createMerchantUserQuery();
    }

    /**
     * @param int|null $idMerchantUser
     * @return \Orm\Zed\Merchant\Persistence\DstMerchantUserQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryMerchantUserById(?int $idMerchantUser): DstMerchantUserQuery
    {
        return $this
            ->queryMerchantUser()
            ->filterByIdMerchantUser($idMerchantUser);
    }

    /**
     * @param string $email
     * @return \Orm\Zed\Merchant\Persistence\DstMerchantUserQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryMerchantUserByEmail(string $email): DstMerchantUserQuery
    {
        return $this
            ->queryMerchantUser()
            ->filterByEmail($email);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryMerchantByIdBranch(int $idBranch): SpyMerchantQuery
    {
        return $this
            ->queryMerchant()
            ->useSpyBranchQuery()
            ->filterByIdBranch($idBranch)
            ->endUse();
    }

    /**
     * {@inheritDoc}
     *
     * @param string $email
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryMerchantByEmail(string $email): SpyMerchantQuery
    {
        return $this
            ->queryMerchant()
            ->filterByMerchantname($email);
    }

    /**
     * @param int $fkBranch
     * @param array $fkDeposits
     * @return DstBranchToDepositQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryBranchToDepositByBranchAndDeposits(int $fkBranch, array $fkDeposits): DstBranchToDepositQuery
    {
        return $this
            ->queryBranchToDeposit()
            ->filterByFkBranch($fkBranch)
            ->filterByFkDeposit($fkDeposits, Criteria::IN);
    }
}
