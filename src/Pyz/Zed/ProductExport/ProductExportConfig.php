<?php
/**
 * Durst - project - ProductExportConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 16:09
 */

namespace Pyz\Zed\ProductExport;


use Pyz\Shared\ProductExport\ProductExportConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class ProductExportConfig extends AbstractBundleConfig
{
    public const DEFAULT_BATCH_SIZE = 5;

    /**
     * @return int
     */
    public function getBatchSize(): int
    {
        return $this
            ->get(
                ProductExportConstants::BATCH_SIZE,
                static::DEFAULT_BATCH_SIZE
            );
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this
            ->get(
                ProductExportConstants::FILE_PATH
            );
    }

    /**
     * @return string
     */
    public function getProjectTimezone(): string
    {
        return $this
            ->get(
                ApplicationConstants::PROJECT_TIMEZONE
            );
    }
}
