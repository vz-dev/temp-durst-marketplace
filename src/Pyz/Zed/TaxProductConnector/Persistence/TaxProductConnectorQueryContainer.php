<?php
/**
 * Durst - project - TaxProductConnectorQueryContainer.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.06.20
 * Time: 18:29
 */

namespace Pyz\Zed\TaxProductConnector\Persistence;

use DateTime;
use Orm\Zed\Product\Persistence\Map\SpyProductAbstractTableMap;
use Orm\Zed\Tax\Persistence\Map\SpyTaxRateTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Shared\Tax\TaxConstants;
use Spryker\Zed\TaxProductConnector\Persistence\TaxProductConnectorQueryContainer as SprykerTaxProductConnectorQueryContainer;

class TaxProductConnectorQueryContainer extends SprykerTaxProductConnectorQueryContainer implements TaxProductConnectorQueryContainerInterface
{
    /**
     * @param array $allIdProductAbstracts
     * @param $countryIso2Code
     * @param \DateTime $date
     *
     * @return \Orm\Zed\Tax\Persistence\SpyTaxSetQuery
     */
    public function queryTaxSetByIdProductAbstractAndCountryIso2CodeForDate(
        array $allIdProductAbstracts,
        $countryIso2Code,
        DateTime $date
    ) {
        $dateString = $date->format('Y-m-d');
        return $this
            ->getFactory()
            ->createTaxSetQuery()
            ->useSpyProductAbstractQuery()
                ->filterByIdProductAbstract($allIdProductAbstracts, Criteria::IN)
                ->withColumn(SpyProductAbstractTableMap::COL_ID_PRODUCT_ABSTRACT, self::COL_ID_ABSTRACT_PRODUCT)
                ->groupBy(SpyProductAbstractTableMap::COL_ID_PRODUCT_ABSTRACT)
            ->endUse()
            ->useSpyTaxSetTaxQuery()
                ->useSpyTaxRateQuery()
                    ->useCountryQuery()
                        ->filterByIso2Code($countryIso2Code)
                    ->endUse()
                    ->_or()
                    ->filterByName(TaxConstants::TAX_EXEMPT_PLACEHOLDER)
                    ->filterByValidFrom($dateString, Criteria::LESS_EQUAL)
                    ->_or()
                    ->filterByValidFrom(null, Criteria::ISNULL)
                    ->_and()
                    ->filterByValidTo($dateString, Criteria::GREATER_EQUAL)
                    ->_or()
                    ->filterByValidTo(null, Criteria::ISNULL)
                ->endUse()
                ->withColumn('MAX(' . SpyTaxRateTableMap::COL_RATE . ')', self::COL_MAX_TAX_RATE)
            ->endUse()
            ->select([self::COL_MAX_TAX_RATE]);
    }

    /**
     * @param array $allIdProductAbstracts
     * @param string $countryIso2Code
     *
     * @return \Orm\Zed\Tax\Persistence\SpyTaxSetQuery
     */
    public function queryTaxSetByIdProductAbstractAndCountryIso2Code(
        array $allIdProductAbstracts,
        $countryIso2Code
    ) {
        return $this
            ->queryTaxSetByIdProductAbstractAndCountryIso2CodeForDate(
                $allIdProductAbstracts,
                $countryIso2Code,
                $this->getCurrentDate()
            );
    }

    /**
     * @return \DateTime
     */
    protected function getCurrentDate(): DateTime
    {
        return new DateTime('now');
    }
}
