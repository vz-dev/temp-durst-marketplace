<?php
/**
 * Durst - project - ConcreteTimeSlotCollectorStoragePlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 16.10.18
 * Time: 22:19
 */

namespace Pyz\Zed\Collector\Communication\Plugin;


use Generated\Shared\Transfer\LocaleTransfer;
use Orm\Zed\Touch\Persistence\SpyTouchQuery;
use Pyz\Zed\Collector\Business\CollectorFacadeInterface;
use Spryker\Zed\Collector\Business\Exporter\Reader\ReaderInterface;
use Spryker\Zed\Collector\Business\Exporter\Writer\TouchUpdaterInterface;
use Spryker\Zed\Collector\Business\Exporter\Writer\WriterInterface;
use Spryker\Zed\Collector\Business\Model\BatchResultInterface;
use Spryker\Zed\Collector\Communication\Plugin\AbstractCollectorPlugin;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConcreteTimeSlotCollectorStoragePlugin
 * @package Pyz\Zed\Collector\Communication\Plugin
 * @method CollectorFacadeInterface getFacade()
 */
class ConcreteTimeSlotCollectorStoragePlugin extends AbstractCollectorPlugin
{

    /**
     * @api
     *
     * @param \Orm\Zed\Touch\Persistence\SpyTouchQuery $baseQuery
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Spryker\Zed\Collector\Business\Model\BatchResultInterface $result
     * @param \Spryker\Zed\Collector\Business\Exporter\Reader\ReaderInterface $dataReader
     * @param \Spryker\Zed\Collector\Business\Exporter\Writer\WriterInterface $dataWriter
     * @param \Spryker\Zed\Collector\Business\Exporter\Writer\TouchUpdaterInterface $touchUpdater
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function run(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $locale,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    )
    {
        $this
            ->getFacade()
            ->runStorageConcreteTimeSlotCollector(
                $baseQuery,
                $locale,
                $result,
                $dataReader,
                $dataWriter,
                $touchUpdater,
                $output
            );
    }
}