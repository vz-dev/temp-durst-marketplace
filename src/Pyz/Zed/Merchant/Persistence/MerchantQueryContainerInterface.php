<?php
/**
 * Durst - project - MerchantQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 10:56
 */

namespace Pyz\Zed\Merchant\Persistence;

use Orm\Zed\Merchant\Persistence\DstBranchToDepositQuery;
use Orm\Zed\Merchant\Persistence\DstBranchUserQuery;
use Orm\Zed\Merchant\Persistence\DstMerchantUserQuery;
use Orm\Zed\Merchant\Persistence\SpyBranchQuery;
use Orm\Zed\Merchant\Persistence\SpyBranchToPaymentMethodQuery;
use Orm\Zed\Merchant\Persistence\SpyEnumSalutationQuery;
use Orm\Zed\Merchant\Persistence\SpyMerchantQuery;
use Orm\Zed\Merchant\Persistence\SpyPaymentMethodQuery;

interface MerchantQueryContainerInterface
{
    /**
     * Returns a merchant query filtered by a merchant name. As this column is unique
     * this will always return one result at max
     *
     * @param string $merchantname
     *
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     */
    public function queryMerchantByMerchantname(string $merchantname): SpyMerchantQuery;

    /**
     * Returns a merchant query filtered by its primary key. As this column is unique
     * this will always return one result at max
     *
     * @param int $id
     *
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     */
    public function queryMerchantById(int $id): SpyMerchantQuery;

    /**
     * Returns a query query filtered by a merchant name. As this column is unique
     * this will always return one result at max
     *
     * @param int $id
     *
     * @return \Orm\Zed\Merchant\Persistence\SpyBranchQuery
     */
    public function queryBranchById(int $id): SpyBranchQuery;

    /**
     * Returns an unfiltered merchant query
     *
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     */
    public function queryMerchant(): SpyMerchantQuery;

    /**
     * Returns an unfiltered branch query
     *
     * @return \Orm\Zed\Merchant\Persistence\SpyBranchQuery
     */
    public function queryBranch(): SpyBranchQuery;

    /**
     * Returns a branch query filtered by a merchant id. This will return
     * all branches related to a given merchant
     *
     * @param int $idMerchant
     * @return \Orm\Zed\Merchant\Persistence\SpyBranchQuery
     */
    public function queryBranchByIdMerchant(int $idMerchant): SpyBranchQuery;

    /**
     * @param string $name
     * @return SpyBranchQuery
     */
    public function queryBranchByName(string $name): SpyBranchQuery;

    /**
     * @return SpyBranchToPaymentMethodQuery
     */
    public function queryBranchToPaymentMethod(): SpyBranchToPaymentMethodQuery;

    /**
     * @return SpyPaymentMethodQuery
     */
    public function queryPaymentMethod(): SpyPaymentMethodQuery;

    /**
     * @return SpyEnumSalutationQuery
     */
    public function queryEnumSalutation(): SpyEnumSalutationQuery;

    /**
     * @param string $code
     * @return SpyBranchQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryBranchByCode(string $code): SpyBranchQuery;

    /**
     * @return SpyBranchQuery
     */
    public function queryBranchNotDeleted(): SpyBranchQuery;

    /**
     * @return SpyBranchQuery
     */
    public function queryBranchActive(): SpyBranchQuery;

    /**
     * @param int $idMerchant
     * @return SpyPaymentMethodQuery
     */
    public function queryPossiblePaymentMethodsForMerchant(int $idMerchant): SpyPaymentMethodQuery;

    /**
     * @param int $idMerchant
     * @param int $idBranch
     * @return SpyPaymentMethodQuery
     */
    public function queryPossiblePaymentMethodsForMerchantByBranchId(int $idMerchant, int $idBranch): SpyPaymentMethodQuery;

    /**
     * @param string $code
     * @return SpyPaymentMethodQuery
     */
    public function queryPaymentMethodByCode(string $code): SpyPaymentMethodQuery;

    /**
     * @param int $idBranch
     *
     * @return \Orm\Zed\Merchant\Persistence\DstBranchToDepositQuery
     */
    public function queryBranchToDepositWithDeposits(int $idBranch): DstBranchToDepositQuery;

    /**
     * @return \Orm\Zed\Merchant\Persistence\DstBranchToDepositQuery
     */
    public function queryBranchToDeposit(): DstBranchToDepositQuery;

    /**
     * @param int $idBranch
     *
     * @return \Orm\Zed\Merchant\Persistence\DstBranchToDepositQuery
     */
    public function queryBranchToDepositWithAcceptedDeposits(int $idBranch): DstBranchToDepositQuery;

    /**
     * @param int $idBranch
     * @return \Orm\Zed\Merchant\Persistence\DstBranchToDepositQuery
     */
    public function queryBranchToDepositByIdBranch(int $idBranch): DstBranchToDepositQuery;

    /**
     * @param string $merchantPin
     * @return SpyMerchantQuery
     */
    public function queryMerchantByMerchantPin(string $merchantPin): SpyMerchantQuery;

    /**
     * @return \Orm\Zed\Merchant\Persistence\DstBranchUserQuery
     */
    public function queryBranchUser(): DstBranchUserQuery;

    /**
     * @param int|null $idBranchUser
     * @return \Orm\Zed\Merchant\Persistence\DstBranchUserQuery
     */
    public function queryBranchUserById(?int $idBranchUser): DstBranchUserQuery;

    /**
     * @param string $email
     * @return \Orm\Zed\Merchant\Persistence\DstBranchUserQuery
     */
    public function queryBranchUserByEmail(string $email): DstBranchUserQuery;

    /**
     * @return \Orm\Zed\Merchant\Persistence\DstMerchantUserQuery
     */
    public function queryMerchantUser(): DstMerchantUserQuery;

    /**
     * @param int|null $idMerchantUser
     * @return \Orm\Zed\Merchant\Persistence\DstMerchantUserQuery
     */
    public function queryMerchantUserById(?int $idMerchantUser): DstMerchantUserQuery;

    /**
     * @param string $email
     * @return \Orm\Zed\Merchant\Persistence\DstMerchantUserQuery
     */
    public function queryMerchantUserByEmail(string $email): DstMerchantUserQuery;

    /**
     * @param int $idBranch
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     */
    public function queryMerchantByIdBranch(int $idBranch): SpyMerchantQuery;

    /**
     * @param string $email
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     */
    public function queryMerchantByEmail(string $email): SpyMerchantQuery;

    /**
     * @param int $fkBranch
     * @param array $fkDeposits
     * @return DstBranchToDepositQuery
     */
    public function queryBranchToDepositByBranchAndDeposits(int $fkBranch, array $fkDeposits): DstBranchToDepositQuery;
}
