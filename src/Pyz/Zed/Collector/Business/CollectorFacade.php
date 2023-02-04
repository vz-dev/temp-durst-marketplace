<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Collector\Business;

use Generated\Shared\Transfer\LocaleTransfer;
use Orm\Zed\Touch\Persistence\SpyTouchQuery;
use Spryker\Zed\Collector\Business\CollectorFacade as SprykerCollectorFacade;
use Spryker\Zed\Collector\Business\Exporter\Reader\ReaderInterface;
use Spryker\Zed\Collector\Business\Exporter\Writer\TouchUpdaterInterface;
use Spryker\Zed\Collector\Business\Exporter\Writer\WriterInterface;
use Spryker\Zed\Collector\Business\Model\BatchResultInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @SuppressWarnings(PHPMD.ArgumentsFacadeRule)
 *
 * @method CollectorBusinessFactory getFactory()
 */
class CollectorFacade extends SprykerCollectorFacade implements CollectorFacadeInterface
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
    ) {

        $collector = $this->getFactory()
            ->createSearchProductCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

    /**
     * {@inheritdoc}
     *
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
        OutputInterface $output)
    {
        $collector = $this->getFactory()
            ->createSearchDeliveryAreaCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

    /**
     * {@inheritdoc}
     *
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
        OutputInterface $output)
    {
        $collector = $this->getFactory()
            ->createSearchBranchCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

    /**
     * {@inheritdoc}
     *
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
        OutputInterface $output)
    {
        $collector = $this->getFactory()
            ->createSearchPriceCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

    /**
     * {@inheritdoc}
     *
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
        OutputInterface $output)
    {
        $collector = $this->getFactory()
            ->createSearchCategoryCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

    /**
     * {@inheritdoc}
     *
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
        OutputInterface $output)
    {
        $collector = $this->getFactory()
            ->createSearchTimeslotCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

    /**
     * {@inheritdoc}
     *
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
        OutputInterface $output)
    {
        $collector = $this->getFactory()
            ->createSearchPaymentProviderCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

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
    ) {

        $collector = $this->getFactory()
            ->createStorageCategoryNodeCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

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
    ) {

        $collector = $this->getFactory()
            ->createStorageNavigationCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

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
    public function runSearchCategoryNodeCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    ) {

        $collector = $this->getFactory()
            ->createSearchCategoryNodeCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

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
    ) {

        $collector = $this->getFactory()
            ->createStorageProductAbstractCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

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
    ) {

        $collector = $this->getFactory()
            ->createStorageRedirectCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

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
    ) {

        $collector = $this->getFactory()
            ->createStorageTranslationCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

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
    ) {

        $collector = $this->getFactory()
            ->createStorageUrlCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

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
    public function runStorageProductConcreteCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    ) {
        $collector = $this->getFactory()->createStorageProductConcreteCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

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
    ) {
        $collector = $this->getFactory()->createAttributeMapCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

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
    public function runStorageProductOptionCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    ) {

        $collector = $this->getFactory()
            ->createStorageProductOptionCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

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
    public function runStorageAvailabilityCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    ) {

        $collector = $this->getFactory()
            ->createStorageAvailabilityCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

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
    ) {

        $collector = $this->getFactory()
            ->createStorageConcreteTimeSlotCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param OutputInterface $output
     *
     * @return BatchResultInterface[]
     */
    public function exportTimeSlotSearch(OutputInterface $output) : array
    {
        $exporter = $this->getFactory()->createTimeSlotSearchExporter();

        return $exporter->exportStorage($output);
    }

    /**
     * {@inheritdoc}
     *
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
        OutputInterface $output)
    {
        $collector = $this->getFactory()
            ->createSearchGMTimeslotCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param OutputInterface $output
     *
     * @return BatchResultInterface[]
     */
    public function exportGMTimeSlotSearch(OutputInterface $output) : array
    {
        $exporter = $this->getFactory()->createGMTimeSlotSearchExporter();

        return $exporter->exportStorage($output);
    }

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
    ) {

        $collector = $this->getFactory()
            ->createStorageGMSettingsCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }

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
    ) {

        $collector = $this->getFactory()
            ->createStorageAbsenceCollector();

        $this->runCollector(
            $collector,
            $baseQuery,
            $localeTransfer,
            $result,
            $dataReader,
            $dataWriter,
            $touchUpdater,
            $output
        );
    }
}
