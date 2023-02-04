<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 13.11.18
 * Time: 10:32
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
 * Class PaymentProviderCollectorSearchPlugin
 * @package Pyz\Zed\Collector\Communication\Plugin
 * @method CollectorFacadeInterface getFacade()
 */
class PaymentProviderCollectorSearchPlugin extends AbstractCollectorPlugin
{

    /**
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
    public function run(SpyTouchQuery $baseQuery,
                        LocaleTransfer $locale,
                        BatchResultInterface $result,
                        ReaderInterface $dataReader,
                        WriterInterface $dataWriter,
                        TouchUpdaterInterface $touchUpdater,
                        OutputInterface $output)
    {
        $this
            ->getFacade()
            ->runSearchPaymentProviderCollector(
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