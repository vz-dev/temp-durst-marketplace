<?php
/**
 * Durst - project - PriceImportConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 10:57
 */

namespace Pyz\Zed\PriceImport;


use Pyz\Shared\PriceImport\PriceImportConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class PriceImportConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getPriceImportFolder(): string
    {
        return $this
            ->get(
                PriceImportConstants::UPLOAD_PRICE_IMPORT_FOLDER
            );
    }
}
