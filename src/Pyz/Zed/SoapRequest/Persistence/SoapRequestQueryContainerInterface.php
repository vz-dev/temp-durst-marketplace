<?php
/**
 * Durst - project - SoapRequestQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-02
 * Time: 15:21
 */

namespace Pyz\Zed\SoapRequest\Persistence;


use Orm\Zed\SoapRequest\Persistence\DstSoapRequestQuery;

interface SoapRequestQueryContainerInterface
{
    /**
     * @return DstSoapRequestQuery
     */
    public function querySoapRequest(): DstSoapRequestQuery;

    /**
     * @param int $idSoapRequest
     * @return DstSoapRequestQuery
     */
    public function querySoapRequestById(int $idSoapRequest): DstSoapRequestQuery;
}
