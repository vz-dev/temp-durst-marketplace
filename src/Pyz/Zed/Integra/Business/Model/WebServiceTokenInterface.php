<?php
/**
 * Durst - project - WebServiceTokenInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-09
 * Time: 11:40
 */

namespace Pyz\Zed\Integra\Business\Model;

use Generated\Shared\Transfer\IntegraWebserviceTokenTransfer;

interface WebServiceTokenInterface
{
    /**
     * @param int $idBranch
     *
     * @return IntegraWebserviceTokenTransfer
     */
    public function getCurrentTokenForBranch(int $idBranch) : IntegraWebserviceTokenTransfer;
}
