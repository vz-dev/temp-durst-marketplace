<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Collector\Business;

use Generated\Shared\Transfer\LocaleTransfer;
use Orm\Zed\Touch\Persistence\SpyTouchQuery;
use Spryker\Zed\Collector\Business\CollectorFacadeInterface as SprykerCollectorFacadeInterface;
use Spryker\Zed\Collector\Business\Exporter\Reader\ReaderInterface;
use Spryker\Zed\Collector\Business\Exporter\Writer\TouchUpdaterInterface;
use Spryker\Zed\Collector\Business\Exporter\Writer\WriterInterface;
use Spryker\Zed\Collector\Business\Model\BatchResultInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CollectorFacadeInterface extends SprykerCollectorFacadeInterface
{
    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runSearchProductCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runSearchDeliveryAreaCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runSearchBranchCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runSearchPriceCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runSearchCategoryCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runSearchTimeslotCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runSearchPaymentProviderCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runStorageCategoryNodeCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runStorageNavigationCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runStorageProductAbstractCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runStorageRedirectCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runStorageTranslationCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runStorageUrlCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runStorageAttributeMapCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runStorageConcreteTimeSlotCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * Specification:
     * - Runs search exporter collectors for all available stores, locales and collector types
     *  for the time slot index
     *
     * @param OutputInterface $output
     *
     * @return BatchResultInterface[]
     */
    public function exportTimeSlotSearch(OutputInterface $output) : array;


    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runSearchGMTimeslotCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    public function exportGMTimeSlotSearch(OutputInterface $output) : array;


    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     */
    public function runStorageGMSettingsCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );

    /**
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $localeTransfer
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     */
    public function runStorageAbsenceCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    );
}
