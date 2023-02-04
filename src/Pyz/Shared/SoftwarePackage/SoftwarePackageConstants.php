<?php
/**
 * Durst - project - SoftwarePackageConstants.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 24.10.18
 * Time: 20:26
 */

namespace Pyz\Shared\SoftwarePackage;


interface SoftwarePackageConstants
{
    public const SOFTWARE_PACKAGE_RETAIL_CODE = 'digitaler_heimservice';
    public const SOFTWARE_PACKAGE_WHOLESALE_CODE = 'digitale_sofort_lieferung';

    public const SOFTWARE_FEATURE_ALLOW_COMMENTS = 'SOFTWARE_FEATURE_ALLOW_COMMENTS';

    public const IMPORT_LICENSE_KEY_DATE_FORMAT = 'Y-m-d';

    public const LICENSE_KEY_KEY = 'LICENSE_KEY_KEY';
    public const LICENSE_KEY_VI = 'LICENSE_KEY_VI';
    public const LICENSE_KEY_METHOD = 'LICENSE_KEY_METHOD';
}
