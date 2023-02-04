<?php
/**
 * Durst - project - RealaxExportFixedInputStreamPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.09.20
 * Time: 10:27
 */

namespace Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter;


use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\InputStreamPluginInterface;

/**
 * Class RealaxExportFixedInputStreamPlugin
 * @package Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter
 * @method \Pyz\Zed\Accounting\Communication\AccountingCommunicationFactory getFactory()
 */
class RealaxExportFixedInputStreamPlugin extends AbstractPlugin implements InputStreamPluginInterface
{
    protected const PLUGIN_NAME = 'RealaxExportFixedInputStreamPlugin';

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
            ->createRealaxExportFixedReadStream($idMerchant);
    }
}
