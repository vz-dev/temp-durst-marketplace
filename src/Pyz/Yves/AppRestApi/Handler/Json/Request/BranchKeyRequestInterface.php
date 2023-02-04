<?php
/**
 * Durst - project - BranchKeyRequestInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.05.18
 * Time: 15:29
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;

interface BranchKeyRequestInterface
{
    public const KEY_ZIP_CODE = 'zip_code';
    public const KEY_MERCHANT_ID = 'merchant_id';
    public const KEY_BRANCH_CODE = 'branch_code';
}
