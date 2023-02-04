<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Console;

use Pyz\Shared\Config\Environment;
use Pyz\Zed\Accounting\Communication\Console\RealaxExportConsole;
use Pyz\Zed\Accounting\Communication\Console\RealaxExportFixedConsole;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Console\AkeneoImportAttributesConsole;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Console\AkeneoImportCategoriesConsole;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Console\AkeneoImportConsole;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Console\AkeneoImportProductAbstractConsole;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Console\AkeneoImportProductConcreteConsole;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Console\AkeneoMapAttributesConsole;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Console\AkeneoMapLocalesConsole;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Console\AkeneoMapProductAbstractConsole;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Console\AkeneoMapProductConcreteConsole;
use Pyz\Zed\Billing\Communication\Console\CreateBillingItemConsole;
use Pyz\Zed\Billing\Communication\Console\CreateBillingItemsForBillingPeriodConsole;
use Pyz\Zed\Billing\Communication\Console\CreateBillingPeriodConsole;
use Pyz\Zed\Billing\Communication\Console\CreateNextBillingPeriodForBranchConsole;
use Pyz\Zed\Billing\Communication\Console\RemoveDuplicateEmptyBillingPeriodsConsole;
use Pyz\Zed\Collector\Communication\Console\CollectorGMTimeSlotSearchExportConsole;
use Pyz\Zed\Collector\Communication\Console\CollectorTimeSlotSearchExportConsole;
use Pyz\Zed\DataImport\DataImportConfig;
use Pyz\Zed\DeliveryArea\Communication\Console\CreateConcreteTimeSlotsConsole;
use Pyz\Zed\DeliveryArea\Communication\Console\TouchDeletePassedConcreteTimeSlotsConsole;
use Pyz\Zed\DocumentationGeneratorRestApi\Communication\Console\GenerateRestApiDocumentationConsole;
use Pyz\Zed\GraphMasters\Communication\Console\GraphMastersTimeSlotConsole;
use Pyz\Zed\GraphMasters\Communication\Console\GraphMastersTourFixConsole;
use Pyz\Zed\GraphMasters\Communication\Console\GraphMastersTourFixCutOffReachedConsole;
use Pyz\Zed\GraphMasters\Communication\Console\GraphMastersTourImportConsole;
use Pyz\Zed\Installer\Communication\Console\InitializeDatabaseConsole;
use Pyz\Zed\Integra\Communication\Console\ExportClosedConsole;
use Pyz\Zed\Integra\Communication\Console\ExportOpenConsole;
use Pyz\Zed\Integra\Communication\Console\ImportOrdersConsole;
use Pyz\Zed\Campaign\Communication\Console\IsCampaignBookableConsole;
use Pyz\Zed\MerchantPrice\Communication\Console\ProductRelevanceConsole;
use Pyz\Zed\Oms\Communication\Console\CreateAndSendInvoice;
use Pyz\Zed\Oms\Communication\Console\DetectStuckOrders;
use Pyz\Zed\PriceImport\Communication\Controller\Console\BatchPriceImportConsole;
use Pyz\Zed\Product\Communication\Console\ProductDeactivationConsole;
use Pyz\Zed\Product\Communication\Console\ProductExporterConsole;
use Pyz\Zed\ProductExport\Communication\Controller\Console\BatchProductExportConsole;
use Pyz\Zed\Propel\Communication\Console\DatabaseDropTablesConsole;
use Pyz\Zed\Touch\Communication\Console\TouchAllNowConsole;
use Pyz\Zed\Touch\Communication\Console\TouchSearchTruncateConsole;
use Pyz\Zed\Tour\Communication\Console\DepositEdiConsole;
use Pyz\Zed\Tour\Communication\Console\TourEdiConsole;
use Pyz\Zed\Tour\Communication\Console\TourExportCheckConsole;
use Pyz\Zed\Tour\Communication\Console\TourExportConsole;
use Pyz\Zed\Tour\Communication\Console\TourGeneratorConsole;
use Silex\Provider\TwigServiceProvider;
use Silex\ServiceProviderInterface;
use Spryker\Zed\Cache\Communication\Console\EmptyAllCachesConsole;
use Spryker\Zed\CodeGenerator\Communication\Console\BundleClientCodeGeneratorConsole;
use Spryker\Zed\CodeGenerator\Communication\Console\BundleCodeGeneratorConsole;
use Spryker\Zed\CodeGenerator\Communication\Console\BundleServiceCodeGeneratorConsole;
use Spryker\Zed\CodeGenerator\Communication\Console\BundleSharedCodeGeneratorConsole;
use Spryker\Zed\CodeGenerator\Communication\Console\BundleYvesCodeGeneratorConsole;
use Spryker\Zed\CodeGenerator\Communication\Console\BundleZedCodeGeneratorConsole;
use Spryker\Zed\Collector\Communication\Console\CollectorSearchExportConsole;
use Spryker\Zed\Collector\Communication\Console\CollectorStorageExportConsole;
use Spryker\Zed\Console\Communication\Plugin\ConsoleLogPlugin;
use Spryker\Zed\Console\ConsoleDependencyProvider as SprykerConsoleDependencyProvider;
use Spryker\Zed\CustomersRestApi\Communication\Console\CustomerAddressesUuidWriterConsole;
use Spryker\Zed\DataImport\Communication\Console\DataImportConsole;
use Spryker\Zed\Development\Communication\Console\CodeArchitectureSnifferConsole;
use Spryker\Zed\Development\Communication\Console\CodePhpMessDetectorConsole;
use Spryker\Zed\Development\Communication\Console\CodePhpstanConsole;
use Spryker\Zed\Development\Communication\Console\CodeStyleSnifferConsole;
use Spryker\Zed\Development\Communication\Console\CodeTestConsole;
use Spryker\Zed\Development\Communication\Console\GenerateClientIdeAutoCompletionConsole;
use Spryker\Zed\Development\Communication\Console\GenerateGlueIdeAutoCompletionConsole;
use Spryker\Zed\Development\Communication\Console\GenerateIdeAutoCompletionConsole;
use Spryker\Zed\Development\Communication\Console\GenerateServiceIdeAutoCompletionConsole;
use Spryker\Zed\Development\Communication\Console\GenerateYvesIdeAutoCompletionConsole;
use Spryker\Zed\Development\Communication\Console\GenerateZedIdeAutoCompletionConsole;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Log\Communication\Console\DeleteLogFilesConsole;
use Spryker\Zed\Maintenance\Communication\Console\MaintenanceDisableConsole;
use Spryker\Zed\Maintenance\Communication\Console\MaintenanceEnableConsole;
use Spryker\Zed\Money\Communication\Plugin\ServiceProvider\TwigMoneyServiceProvider;
use Spryker\Zed\NewRelic\Communication\Console\RecordDeploymentConsole;
use Spryker\Zed\NewRelic\Communication\Plugin\NewRelicConsolePlugin;
use Spryker\Zed\Oms\Communication\Console\CheckConditionConsole as OmsCheckConditionConsole;
use Spryker\Zed\Oms\Communication\Console\CheckTimeoutConsole as OmsCheckTimeoutConsole;
use Spryker\Zed\Oms\Communication\Console\ClearLocksConsole as OmsClearLocksConsole;
use Spryker\Zed\Product\Communication\Console\ProductTouchConsole;
use Spryker\Zed\ProductLabel\Communication\Console\ProductLabelRelationUpdaterConsole;
use Spryker\Zed\ProductLabel\Communication\Console\ProductLabelValidityConsole;
use Spryker\Zed\ProductRelation\Communication\Console\ProductRelationUpdaterConsole;
use Spryker\Zed\Propel\Communication\Console\DatabaseDropConsole;
use Spryker\Zed\Propel\Communication\Console\DatabaseExportConsole;
use Spryker\Zed\Propel\Communication\Console\DatabaseImportConsole;
use Spryker\Zed\Propel\Communication\Console\DeleteMigrationFilesConsole;
use Spryker\Zed\Propel\Communication\Console\PropelSchemaValidatorConsole;
use Spryker\Zed\Propel\Communication\Plugin\ServiceProvider\PropelServiceProvider;
use Spryker\Zed\Queue\Communication\Console\QueueTaskConsole;
use Spryker\Zed\Queue\Communication\Console\QueueWorkerConsole;
use Spryker\Zed\RabbitMq\Communication\Console\DeleteAllExchangesConsole;
use Spryker\Zed\RabbitMq\Communication\Console\DeleteAllQueuesConsole;
use Spryker\Zed\RabbitMq\Communication\Console\PurgeAllQueuesConsole;
use Spryker\Zed\RabbitMq\Communication\Console\SetUserPermissionsConsole;
use Spryker\Zed\RestRequestValidator\Communication\Console\BuildValidationCacheConsole;
use Spryker\Zed\Search\Communication\Console\GenerateIndexMapConsole;
use Spryker\Zed\Search\Communication\Console\SearchCloseIndexConsole;
use Spryker\Zed\Search\Communication\Console\SearchConsole;
use Spryker\Zed\Search\Communication\Console\SearchCopyIndexConsole;
use Spryker\Zed\Search\Communication\Console\SearchCreateSnapshotConsole;
use Spryker\Zed\Search\Communication\Console\SearchDeleteIndexConsole;
use Spryker\Zed\Search\Communication\Console\SearchDeleteSnapshotConsole;
use Spryker\Zed\Search\Communication\Console\SearchRegisterSnapshotRepositoryConsole;
use Spryker\Zed\Search\Communication\Console\SearchRestoreSnapshotConsole;
use Spryker\Zed\Session\Communication\Console\SessionRemoveLockConsole;
use Spryker\Zed\Setup\Communication\Console\DeployPreparePropelConsole;
use Spryker\Zed\Setup\Communication\Console\EmptyGeneratedDirectoryConsole;
use Spryker\Zed\Setup\Communication\Console\InstallConsole;
use Spryker\Zed\Setup\Communication\Console\JenkinsDisableConsole;
use Spryker\Zed\Setup\Communication\Console\JenkinsEnableConsole;
use Spryker\Zed\Setup\Communication\Console\JenkinsGenerateConsole;
use Spryker\Zed\Setup\Communication\Console\Npm\RunnerConsole;
use Spryker\Zed\SetupFrontend\Communication\Console\CleanUpDependenciesConsole;
use Spryker\Zed\SetupFrontend\Communication\Console\InstallPackageManagerConsole;
use Spryker\Zed\SetupFrontend\Communication\Console\InstallProjectDependenciesConsole;
use Spryker\Zed\SetupFrontend\Communication\Console\YvesBuildFrontendConsole;
use Spryker\Zed\SetupFrontend\Communication\Console\YvesInstallDependenciesConsole;
use Spryker\Zed\SetupFrontend\Communication\Console\ZedBuildFrontendConsole;
use Spryker\Zed\SetupFrontend\Communication\Console\ZedInstallDependenciesConsole;
use Spryker\Zed\StateMachine\Communication\Console\CheckConditionConsole as StateMachineCheckConditionConsole;
use Spryker\Zed\StateMachine\Communication\Console\CheckTimeoutConsole as StateMachineCheckTimeoutConsole;
use Spryker\Zed\StateMachine\Communication\Console\ClearLocksConsole as StateMachineClearLocksConsole;
use Spryker\Zed\Storage\Communication\Console\StorageDeleteAllConsole;
use Spryker\Zed\Storage\Communication\Console\StorageExportRdbConsole;
use Spryker\Zed\Storage\Communication\Console\StorageImportRdbConsole;
use Spryker\Zed\Touch\Communication\Console\TouchCleanUpConsole;
use Spryker\Zed\Transfer\Communication\Console\DataBuilderGeneratorConsole;
use Spryker\Zed\Transfer\Communication\Console\GeneratorConsole;
use Spryker\Zed\Transfer\Communication\Console\ValidatorConsole;
use Spryker\Zed\Twig\Communication\Console\CacheWarmerConsole;
use Spryker\Zed\Twig\Communication\Plugin\ServiceProvider\TwigServiceProvider as SprykerTwigServiceProvider;
use Spryker\Zed\ZedNavigation\Communication\Console\BuildNavigationConsole;
use SprykerMiddleware\Zed\Process\Communication\Console\ProcessConsole;
use Stecman\Component\Symfony\Console\BashCompletion\CompletionCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConsoleDependencyProvider extends SprykerConsoleDependencyProvider
{
    /**
     * @param Container $container
     *
     * @return Command[]
     */
    protected function getConsoleCommands(Container $container)
    {
        $commands = [
            new CacheWarmerConsole(),
            new BuildNavigationConsole(),
            new CollectorStorageExportConsole(),
            new CollectorTimeSlotSearchExportConsole(),
            new CollectorGMTimeSlotSearchExportConsole(),
            new CollectorSearchExportConsole(),
            new TouchCleanUpConsole(),
            new EmptyAllCachesConsole(),
            new GeneratorConsole(),
            new InitializeDatabaseConsole(),
            new RecordDeploymentConsole(),
            new SearchConsole(),
            new GenerateIndexMapConsole(),
            new OmsCheckConditionConsole(),
            new OmsCheckTimeoutConsole(),
            new OmsClearLocksConsole(),
            new StateMachineCheckTimeoutConsole(),
            new StateMachineCheckConditionConsole(),
            new StateMachineClearLocksConsole(),
            new SessionRemoveLockConsole(),
            new QueueTaskConsole(),
            new QueueWorkerConsole(),
            new ProductRelationUpdaterConsole(),
            new ProductLabelValidityConsole(),
            new ProductLabelRelationUpdaterConsole(),

            // Akeneo importer
            new ProcessConsole(),

            new DataImportConsole(),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_STORE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_CURRENCY),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_CATEGORY_TEMPLATE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_CUSTOMER),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_GLOSSARY),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_NAVIGATION),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_NAVIGATION_NODE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_CMS_TEMPLATE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_CMS_PAGE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_CMS_BLOCK),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_CMS_BLOCK_CATEGORY_POSITION),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_CMS_BLOCK_CATEGORY),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_DISCOUNT),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_DISCOUNT_VOUCHER),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_PRODUCT_PRICE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_PRODUCT_STOCK),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_PRODUCT_ATTRIBUTE_KEY),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_PRODUCT_MANAGEMENT_ATTRIBUTE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_PRODUCT_GROUP),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_PRODUCT_OPTION),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_PRODUCT_RELATION),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_PRODUCT_REVIEW),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_PRODUCT_LABEL),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_PRODUCT_SEARCH_ATTRIBUTE_MAP),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_PRODUCT_SEARCH_ATTRIBUTE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_SHIPMENT),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_SHIPMENT_PRICE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_STOCK),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_TAX),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_DISCOUNT_AMOUNT),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_DEPOSIT),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_PRICE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_ENUM_SALUTATION),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_PAYMENT_METHOD),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_BRANCH),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_DELIVERY_AREA),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_TIME_SLOT),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_MERCHANT),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_CATEGORY_STYLE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_TERMS_OF_SERVICE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_MANUFACTURER),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_SOFTWARE_PACKAGE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_SOFTWARE_FEATURE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_VEHICLE_TYPE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_VEHICLE_CATEGORY),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_LICENSE_KEY),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_TOUR),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_DEPOSIT_SKU),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_DRIVING_LICENCE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_DRIVER),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_CONCRETE_TOUR),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_CONCRETE_TIME_SLOT),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_ORDERS),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_DRIVER_APP_RELEASE),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_BRANCH_USER),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_GRAPHMASTERS_SETTINGS),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_GRAPHMASTERS_DELIVERY_AREA_CATEGORY),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_GRAPHMASTERS_OPENING_TIMES),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_GRAPHMASTERS_COMMISSIONING_TIMES),
            new DataImportConsole(DataImportConsole::DEFAULT_NAME . ':' . DataImportConfig::IMPORT_TYPE_MERCHANT_USER),

            // Akeneo Import commands
            new AkeneoMapLocalesConsole(),
            new AkeneoMapAttributesConsole(),
            new AkeneoImportCategoriesConsole(),
            new AkeneoImportAttributesConsole(),
            new AkeneoMapProductAbstractConsole(),
            new AkeneoImportProductAbstractConsole(),
            new AkeneoMapProductConcreteConsole(),
            new AkeneoImportProductConcreteConsole(),
            new AkeneoImportConsole(),

            // Product Exporter
            new ProductExporterConsole(),
            new ProductDeactivationConsole(),
            new BatchProductExportConsole(),

            // Price Import
            new BatchPriceImportConsole(),

            // Tour Generator
            new TourGeneratorConsole(),

            // Tour Export Checker
            new TourExportCheckConsole(),
            // Tour Exporter
            new TourExportConsole(),
            // Tour EDI Exporter
            new TourEdiConsole(),

            //  Deposit EDI Exporter
            new DepositEdiConsole(),

            // Concrete Time Slots
            new CreateConcreteTimeSlotsConsole(),
            new TouchDeletePassedConcreteTimeSlotsConsole(),

            // Product relevance
            new ProductRelevanceConsole(),

            // Is campaign bookable
            new IsCampaignBookableConsole(),

            // Docker
            new TouchAllNowConsole(),
            new TouchSearchTruncateConsole(),

            // Billing
            new CreateBillingPeriodConsole(),
            new CreateBillingItemConsole(),
            new CreateBillingItemsForBillingPeriodConsole(),
            new CreateNextBillingPeriodForBranchConsole(),
            new RemoveDuplicateEmptyBillingPeriodsConsole(),

            // Realax csv invoice files
            new RealaxExportConsole(),
            new RealaxExportFixedConsole(),

            // OMS
            new CreateAndSendInvoice(),
            new DetectStuckOrders(),

            // Integra
            new ExportOpenConsole(),
            new ExportClosedConsole(),
            new ImportOrdersConsole(),

            // Graphmasters
            new GraphMastersTimeSlotConsole(),
            new GraphMastersTourImportConsole(),
            new GraphMastersTourFixConsole(),
            new GraphMastersTourFixCutOffReachedConsole(),

            // Setup commands
            new RunnerConsole(),
            new EmptyGeneratedDirectoryConsole(),
            new InstallConsole(),
            new JenkinsEnableConsole(),
            new JenkinsDisableConsole(),
            new JenkinsGenerateConsole(),
            new DeployPreparePropelConsole(),

            new DatabaseDropConsole(),
            new DatabaseDropTablesConsole(),

            new DatabaseExportConsole(),
            new DatabaseImportConsole(),
            new DeleteMigrationFilesConsole(),

            new DeleteLogFilesConsole(),
            new StorageExportRdbConsole(),
            new StorageImportRdbConsole(),
            new StorageDeleteAllConsole(),
            new SearchDeleteIndexConsole(),
            new SearchCloseIndexConsole(),
            new SearchRegisterSnapshotRepositoryConsole(),
            new SearchDeleteSnapshotConsole(),
            new SearchCreateSnapshotConsole(),
            new SearchRestoreSnapshotConsole(),
            new SearchCopyIndexConsole(),

            new InstallPackageManagerConsole(),
            new CleanUpDependenciesConsole(),
            new InstallProjectDependenciesConsole(),

            new YvesInstallDependenciesConsole(),
            new YvesBuildFrontendConsole(),

            new ZedInstallDependenciesConsole(),
            new ZedBuildFrontendConsole(),

            new DeleteAllQueuesConsole(),
            new PurgeAllQueuesConsole(),
            new DeleteAllExchangesConsole(),
            new SetUserPermissionsConsole(),

            new MaintenanceEnableConsole(),
            new MaintenanceDisableConsole(),

            // Glue
            new BuildValidationCacheConsole(),
            new CustomerAddressesUuidWriterConsole(),

            // Documentation
            new GenerateRestApiDocumentationConsole(),
        ];

        $propelCommands = $container->getLocator()->propel()->facade()->getConsoleCommands();
        $commands = array_merge($commands, $propelCommands);

        if (Environment::isDevelopment() || Environment::isTesting()) {
            $commands[] = new CodeTestConsole();
            $commands[] = new CodeStyleSnifferConsole();
            $commands[] = new CodeArchitectureSnifferConsole();
            $commands[] = new CodePhpstanConsole();
            $commands[] = new CodePhpMessDetectorConsole();
            $commands[] = new ProductTouchConsole();
            $commands[] = new ValidatorConsole();
            $commands[] = new BundleCodeGeneratorConsole();
            $commands[] = new BundleYvesCodeGeneratorConsole();
            $commands[] = new BundleZedCodeGeneratorConsole();
            $commands[] = new BundleServiceCodeGeneratorConsole();
            $commands[] = new BundleSharedCodeGeneratorConsole();
            $commands[] = new BundleClientCodeGeneratorConsole();
            $commands[] = new GenerateZedIdeAutoCompletionConsole();
            $commands[] = new GenerateClientIdeAutoCompletionConsole();
            $commands[] = new GenerateServiceIdeAutoCompletionConsole();
            $commands[] = new GenerateYvesIdeAutoCompletionConsole();
            $commands[] = new GenerateGlueIdeAutoCompletionConsole();
            $commands[] = new GenerateIdeAutoCompletionConsole();
            $commands[] = new DataBuilderGeneratorConsole();
            $commands[] = new CompletionCommand();
            $commands[] = new DataBuilderGeneratorConsole();
            $commands[] = new PropelSchemaValidatorConsole();
        }

        return $commands;
    }

    /**
     * @param Container $container
     *
     * @return EventSubscriberInterface[]
     */
    public function getEventSubscriber(Container $container)
    {
        $eventSubscriber = parent::getEventSubscriber($container);

        if (!Environment::isDevelopment()) {
            $eventSubscriber[] = new ConsoleLogPlugin();
            $eventSubscriber[] = new NewRelicConsolePlugin();
        }

        return $eventSubscriber;
    }

    /**
     * @param Container $container
     *
     * @return ServiceProviderInterface[]
     */
    public function getServiceProviders(Container $container)
    {
        $serviceProviders = parent::getServiceProviders($container);
        $serviceProviders[] = new PropelServiceProvider();
        $serviceProviders[] = new TwigServiceProvider();
        $serviceProviders[] = new SprykerTwigServiceProvider();
        $serviceProviders[] = new TwigMoneyServiceProvider();

        return $serviceProviders;
    }
}
