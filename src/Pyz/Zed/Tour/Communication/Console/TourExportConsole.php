<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-12-12
 * Time: 13:37
 */

namespace Pyz\Zed\Tour\Communication\Console;


use Exception;
use Orm\Zed\Edifact\Persistence\Map\DstEdifactExportLogTableMap;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TourExportConsole
 * @package Pyz\Zed\Tour\Communication\Console
 * @method TourFacadeInterface getFacade()
 * @method TourCommunicationFactory getFactory()
 */
class TourExportConsole extends Console
{
    protected const COMMAND_NAME = 'tour:export';
    protected const COMMAND_DESCRIPTION = 'Creates a csv export for further usage.';

    protected const KEY_COMMAND = 'command';

    /**
     * @var \Pyz\Zed\Edifact\Business\EdifactFacadeInterface
     */
    protected $edifactLogger;

    /**
     * @return void
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        $this->edifactLogger = $this
            ->getFactory()
            ->getEdifactFacade();

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info(self::COMMAND_DESCRIPTION);

        $command = $this
            ->getApplication()
            ->find(TourEdiConsole::COMMAND_NAME);

        $exportTransfers = $this
            ->getFacade()
            ->getConcreteToursForEdiExport();

        $message = sprintf(
            'Found %d concrete tour(s) to export.',
            count($exportTransfers)
        );

        $this
            ->info($message);

        if (count($exportTransfers) > 0) {
            $this
                ->edifactLogger
                ->logNonEdi(
                    '',
                    200,
                    $message,
                    DstEdifactExportLogTableMap::COL_LOG_LEVEL_INFO
                );
        }

        $returnCodes = [];

        foreach ($exportTransfers as $exportTransfer) {
            $concreteTour = $exportTransfer
                ->getConcreteTour();

            $branch = $exportTransfer
                ->getBranch();

            $endPointUrl = $branch->getEdiEndpointUrl();

            if (empty($endPointUrl) || $endPointUrl === null) {
                $this
                    ->edifactLogger
                    ->logNonEdi(
                        '',
                        500,
                        sprintf(
                            'Please check branch %s, endpoint is empty or null',
                            $branch->getEmail()
                        ),
                        DstEdifactExportLogTableMap::COL_LOG_LEVEL_ALERT
                    );

                continue;
            }

            $this
                ->getFacade()
                ->setConcreteTourExportInProgress($exportTransfer->getIdConcreteTourExport());

            $message = sprintf(
                'Exporting concrete tour (%d) to endpoint \'%s\'.',
                $concreteTour->getIdConcreteTour(),
                $endPointUrl
            );

            $this
                ->edifactLogger
                ->logNonEdi(
                    $endPointUrl,
                    200,
                    sprintf(
                        'Exporting concrete tour (%d)',
                        $concreteTour->getIdConcreteTour()
                    ),
                    DstEdifactExportLogTableMap::COL_LOG_LEVEL_INFO
                );

            $this
                ->info($message);

            $arguments = [
                self::KEY_COMMAND => TourEdiConsole::COMMAND_NAME,
                TourEdiConsole::OPTION_CONCRETE_TOUR_NAME => $concreteTour->getIdConcreteTour(),
                TourEdiConsole::OPTION_UPLOAD_URL_NAME => $branch->getEdiEndpointUrl()
            ];

            $ediInput = new ArrayInput($arguments);

            $returnCode = $command->run($ediInput, $output);

            $returnCodes[$exportTransfer->getIdConcreteTourExport()] = $returnCode;

            if ($returnCode === 0) {
                $this
                    ->getFacade()
                    ->flagConcreteTourForBeingExported($concreteTour->getIdConcreteTour());

                $this
                    ->getFacade()
                    ->flagConcreteTourForCommissioned($concreteTour->getIdConcreteTour());

                $this
                    ->getFacade()
                    ->removeConcreteTourExportById($exportTransfer->getIdConcreteTourExport());

                $message = sprintf(
                    'Exported and deleted concrete tour export (%d).',
                    $exportTransfer->getIdConcreteTourExport()
                );

                $this
                    ->edifactLogger
                    ->logNonEdi(
                        $endPointUrl,
                        $returnCode,
                        $message,
                        DstEdifactExportLogTableMap::COL_LOG_LEVEL_INFO
                    );

                $this
                    ->info($message);
            } else {
                $message = sprintf(
                    'Unable to export or delete concrete tour export (%d).',
                    $exportTransfer->getIdConcreteTourExport()
                );

                $this
                    ->edifactLogger
                    ->logNonEdi(
                        $endPointUrl,
                        $returnCode,
                        $message,
                        DstEdifactExportLogTableMap::COL_LOG_LEVEL_ERROR
                    );

                $this
                    ->error($message);
            }
        }

        return 0;
    }
}