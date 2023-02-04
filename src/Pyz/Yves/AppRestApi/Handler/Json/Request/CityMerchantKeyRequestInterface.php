<?php
/**
 * Durst - project - CityMerchantKeyRequestInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-18
 * Time: 09:43
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;


interface CityMerchantKeyRequestInterface
{
    public const KEY_ZIP_CODE = 'zip_code';
    public const KEY_BRANCH_CODE = 'branch_code';
}
