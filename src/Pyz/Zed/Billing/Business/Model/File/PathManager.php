<?php
/**
 * Durst - project - PathManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.05.20
 * Time: 09:00
 */

namespace Pyz\Zed\Billing\Business\Model\File;

use Pyz\Zed\Billing\BillingConfig;
use Symfony\Component\Filesystem\Filesystem;

class PathManager implements PathManagerInterface
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Pyz\Zed\Billing\BillingConfig
     */
    protected $config;

    /**
     * PathManager constructor.
     *
     * @param \Symfony\Component\Filesystem\Filesystem $fileSystem
     * @param \Pyz\Zed\Billing\BillingConfig $config
     */
    public function __construct(
        Filesystem $fileSystem,
        BillingConfig $config
    ) {
        $this->fileSystem = $fileSystem;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function checkZipFilePath(): void
    {
        if ($this->fileSystem->exists($this->config->getBillingPeriodZipArchiveTempPath()) !== true) {
            $this
                ->fileSystem
                ->mkdir($this->config->getBillingPeriodZipArchiveTempPath());
        }
    }
}
