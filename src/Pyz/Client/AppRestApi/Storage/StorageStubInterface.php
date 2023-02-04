<?php
/**
 * Durst - project - StorageStubInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 18.10.21
 * Time: 20:30
 */

namespace Pyz\Client\AppRestApi\Storage;


interface StorageStubInterface
{
    /**
     * @param int $idBranch
     * @return array
     */
    public function getGMSettings(int $idBranch) : array;
}
