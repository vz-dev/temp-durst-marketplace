<?php
/**
 * Durst - project - DeliveryAreaRequestInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.03.21
 * Time: 11:08
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;


interface DeliveryAreaRequestInterface
{
    public const KEY_ZIP_CODE = 'zip_code';
    public const KEY_BRANCH_CODE = 'branch_code';
}
