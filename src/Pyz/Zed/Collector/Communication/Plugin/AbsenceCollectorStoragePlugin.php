<?php
/**
 * Durst - project - AbsenceCollectorStoragePlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 28.12.21
 * Time: 15:20
 */

namespace Pyz\Zed\Collector\Communication\Plugin;


use Generated\Shared\Transfer\LocaleTransfer;
use Orm\Zed\Touch\Persistence\SpyTouchQuery;
use Spryker\Zed\Collector\Business\Exporter\Reader\ReaderInterface;
use Spryker\Zed\Collector\Business\Exporter\Writer\TouchUpdaterInterface;
use Spryker\Zed\Collector\Business\Exporter\Writer\WriterInterface;
use Spryker\Zed\Collector\Business\Model\BatchResultInterface;
use Spryker\Zed\Collector\Communication\Plugin\AbstractCollectorPlugin;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbsenceCollectorStoragePlugin
 * @package Pyz\Zed\Collector\Communication\Plugin
 */
class AbsenceCollectorStoragePlugin extends AbstractCollectorPlugin
{
    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $locale
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
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
        $this->getFacade()
            ->runStorageAbsenceCollector($baseQuery, $locale, $result, $dataReader, $dataWriter, $touchUpdater, $output);
    }
}
