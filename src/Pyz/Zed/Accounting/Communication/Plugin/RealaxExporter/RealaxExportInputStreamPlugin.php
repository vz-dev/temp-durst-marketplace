<?php
/**
 * Durst - project - RealaxExportInputStreamPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 26.03.20
 * Time: 15:53
 */

namespace Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter;


use Pyz\Zed\Accounting\Communication\AccountingCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\InputStreamPluginInterface;

/**
 * Class RealaxExportInputStreamPlugin
 * @package Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter
 * @method AccountingCommunicationFactory getFactory()
 */
class RealaxExportInputStreamPlugin extends AbstractPlugin implements InputStreamPluginInterface
{
    protected const PLUGIN_NAME = 'RealaxExportInputStreamPlugin';

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getName(): string
    {
        return static::PLUGIN_NAME;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $path
     * @return \SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface
     * @throws \Exception
     */
    public function getInputStream(string $path): ReadStreamInterface
    {
        $idMerchant = (int)$path;

        return $this
            ->getFactory()
            ->createRealaxExportReadStream($idMerchant);
    }
}
