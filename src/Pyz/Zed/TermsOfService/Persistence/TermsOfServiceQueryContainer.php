<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 11.01.18
 * Time: 15:57
 */

namespace Pyz\Zed\TermsOfService\Persistence;
use Orm\Zed\TermsOfService\Persistence\Map\SpyTermsOfServiceTableMap;
use Orm\Zed\TermsOfService\Persistence\SpyTermsOfServiceQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\TermsOfService\TermsOfServiceConfig;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * Class TermsOfServiceQueryContainer
 * @package Pyz\Zed\TermsOfService\Persistence
 * @method TermsOfServicePersistenceFactory getFactory()
 * @method TermsOfServiceConfig getConfig()
 */
class TermsOfServiceQueryContainer extends AbstractQueryContainer implements TermsOfServiceQueryContainerInterface
{
    const CONDITION_NOT_CUSTOMER_TERMS = 'CONDITION_NOT_CUSTOMER_TERMS';
    const CONDITION_ACTIVE_UNTIL_NULL = 'CONDITION_ACTIVE_UNTIL_NULL';
    const CONDITION_ACTIVE_UNTIL_AFTER_NOW = 'CONDITION_ACTIVE_UNTIL_AFTER_NOW';
    const CONDITION_NOT_IN_ACCEPTED = 'CONDITION_NOT_IN_ACCEPTED';

    const COMBINED_NULL_OR_AFTER_NOW = 'COMBINED_NULL_OR_AFTER_NOW';
    const COMBINED_NULL_OR_AFTER_NOW_AND_NOT_ACCEPTED_AND_NOT_CUSTOMER_TERMS = 'COMBINED_NULL_OR_AFTER_NOW_AND_NOT_ACCEPTED_AND_NOT_CUSTOMER_TERMS';

    /**
     * {@inheritdoc}
     *
     * @return \Orm\Zed\TermsOfService\Persistence\SpyTermsOfServiceQuery
     */
    public function queryTermsOfService()
    {
        return $this
            ->getFactory()
            ->createTermsOfServiceQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @return \Orm\Zed\TermsOfService\Persistence\SpyMerchantToTermsOfServiceQuery
     */
    public function queryMerchantToTermsOfService()
    {
        return $this
            ->getFactory()
            ->createMerchantToTermsOfServiceQuery();
    }

    /**
     * @param int $idMerchant
     * @return SpyTermsOfServiceQuery
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryAcceptedTermsOfServiceByIdMerchant(int $idMerchant): SpyTermsOfServiceQuery
    {
        return $this
            ->queryTermsOfService()
            ->select([SpyTermsOfServiceTableMap::COL_ID_TERMS_OF_SERVICE])
            ->useSpyMerchantToTermsOfServiceQuery()
                ->filterByFkMerchant($idMerchant)
            ->endUse();
    }

    /**
     * @param $acceptedTermsOfService
     * @return SpyTermsOfServiceQuery
     */
    public function queryUnacceptedTermsOfService($acceptedTermsOfService, string $customerTermsName): SpyTermsOfServiceQuery
    {
        return $this
            ->queryTermsOfService()
            ->condition(
                self::CONDITION_NOT_CUSTOMER_TERMS,
                SpyTermsOfServiceTableMap::COL_NAME . Criteria::NOT_EQUAL . '?',
                $customerTermsName
            )
            ->condition(
                self::CONDITION_ACTIVE_UNTIL_NULL,
                SpyTermsOfServiceTableMap::COL_ACTIVE_UNTIL . Criteria::ISNULL
            )
            ->condition(
                self::CONDITION_ACTIVE_UNTIL_AFTER_NOW,
                SpyTermsOfServiceTableMap::COL_ACTIVE_UNTIL . Criteria::GREATER_THAN . '?',
                time()
            )
            ->condition(
                self::CONDITION_NOT_IN_ACCEPTED,
                SpyTermsOfServiceTableMap::COL_ID_TERMS_OF_SERVICE . Criteria::NOT_IN . '?',
                $acceptedTermsOfService
            )
            ->combine(
                [self::CONDITION_ACTIVE_UNTIL_NULL, self::CONDITION_ACTIVE_UNTIL_AFTER_NOW],
                Criteria::LOGICAL_OR,
                self::COMBINED_NULL_OR_AFTER_NOW
            )
            ->where(
                [self::COMBINED_NULL_OR_AFTER_NOW, self::CONDITION_NOT_IN_ACCEPTED, self::CONDITION_NOT_CUSTOMER_TERMS],
                Criteria::LOGICAL_AND
            );
    }

    /**
     * @return SpyTermsOfServiceQuery
     */
    public function queryActiveTermsOfService() : SpyTermsOfServiceQuery
    {
        return $this
            ->queryTermsOfService()
            ->condition(
                self::CONDITION_ACTIVE_UNTIL_NULL,
                SpyTermsOfServiceTableMap::COL_ACTIVE_UNTIL . Criteria::ISNULL
            )
            ->condition(
                self::CONDITION_ACTIVE_UNTIL_AFTER_NOW,
                SpyTermsOfServiceTableMap::COL_ACTIVE_UNTIL . Criteria::GREATER_THAN . '?',
                time()
            )
            ->where(
                [self::CONDITION_ACTIVE_UNTIL_NULL, self::CONDITION_ACTIVE_UNTIL_AFTER_NOW],
                Criteria::LOGICAL_OR
            );
    }

    /**
     * @param int $timestamp
     * @param string $customerTermsName
     * @return SpyTermsOfServiceQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryActiveCustomerTermsByTimestamp(int $timestamp, string $customerTermsName) : SpyTermsOfServiceQuery
    {
        return $this
            ->queryTermsOfService()
            ->where(
                SpyTermsOfServiceTableMap::COL_ACTIVE_UNTIL . Criteria::GREATER_THAN . '?',
                $timestamp
            )
            ->orderByActiveUntil(Criteria::ASC)
            ->filterByName($customerTermsName);
    }
}
