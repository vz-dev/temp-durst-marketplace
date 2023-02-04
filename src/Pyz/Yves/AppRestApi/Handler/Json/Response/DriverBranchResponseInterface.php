<?php
/**
 * Durst - project - DriverBranchResponseInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-06-04
 * Time: 13:51
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface DriverBranchResponseInterface
{
    public const KEY_AUTH_VALID = DriverLoginResponseInterface::KEY_AUTH_VALID;

    public const KEY_BRANCHES = 'branches';
    public const KEY_BRANCHES_ID = 'id';
    public const KEY_BRANCHES_NAME = 'name';
}