<?php
/**
 * Durst - project - SoapRequestQueryContainer.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-02
 * Time: 15:21
 */

namespace Pyz\Zed\SoapRequest\Persistence;


use Orm\Zed\SoapRequest\Persistence\DstSoapRequestQuery;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * Class SoapRequestQueryContainer
 * @package Pyz\Zed\SoapRequest\Persistence
 * @method SoapRequestPersistenceFactory getFactory()
 */
class SoapRequestQueryContainer extends AbstractQueryContainer implements SoapRequestQueryContainerInterface
{

    /**
     * @return DstSoapRequestQuery
     */
    public function querySoapRequest(): DstSoapRequestQuery
    {
        return $this
            ->getFactory()
            ->createSoapRequestQuery();
    }

    /**
     * @param int $idSoapRequest
     * @return DstSoapRequestQuery
     * @throws AmbiguousComparisonException
     */
    public function querySoapRequestById(int $idSoapRequest): DstSoapRequestQuery
    {
        return $this
            ->getFactory()
            ->createSoapRequestQuery()
            ->filterByIdSoapRequest($idSoapRequest);
    }
}
