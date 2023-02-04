<?php
/**
 * Durst - project - EdiExportManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.01.19
 * Time: 09:38
 */

namespace Pyz\Zed\Tour\Business\Manager;

use Orm\Zed\Edifact\Persistence\Map\DstEdifactExportLogTableMap;
use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;
use Pyz\Zed\Tour\Communication\Plugin\DepositExportConfigurationPlugin;
use Pyz\Zed\Tour\Communication\Plugin\GraphMastersDepositExportConfigurationPlugin;
use Pyz\Zed\Tour\Communication\Plugin\GraphMastersTourExportConfigurationPlugin;
use Pyz\Zed\Tour\Communication\Plugin\TourExportConfigurationPlugin;
use Pyz\Zed\Tour\TourConfig;
use Symfony\Component\Process\Process;

class EdiExportManager implements EdiExportManagerInterface
{
    /**
     * @var TourConfig
     */
    protected $tourConfig;

    /**
     * @var EdifactFacadeInterface
     */
    protected $edifactLogger;

    /**
     * EdiExportManager constructor.
     * @param TourConfig $tourConfig
     * @param EdifactFacadeInterface $edifactLogger
     */
    public function __construct(
        TourConfig $tourConfig,
        EdifactFacadeInterface $edifactLogger
    )
    {
        $this->tourConfig = $tourConfig;
        $this->edifactLogger = $edifactLogger;
    }

    /**
     * @param int $idTour
     * @param string $endpointUrl
     * @param int $timeout
     * @param bool $isGraphmastersTour
     * @param string|null $applicationEnv
     * @param string|null $applicationStore
     * @param string|null $applicationRootDir
     * @param string|null $application
     *
     * @return int
     */
    public function ediExportTourById(
        int $idTour,
        string $endpointUrl,
        int $timeout,
        bool $isGraphmastersTour = false,
        ?string $applicationEnv = null,
        ?string $applicationStore = null,
        ?string $applicationRootDir = null,
        ?string $application = null
    ): int {
        $processName = $isGraphmastersTour === true
            ? GraphMastersTourExportConfigurationPlugin::PROCESS_NAME
            : TourExportConfigurationPlugin::PROCESS_NAME;

        if ($applicationEnv === null || $applicationStore === null || $applicationRootDir === null || $application === null) {
            $command = $this->tourConfig->getPhpPathForConsole()
                . ' vendor/bin/console middleware:process:run'
                . ' -p ' . $processName
                . ' -o ' . $endpointUrl
                . ' -i ' . $idTour;
        } else {
            $command = 'APPLICATION_ENV=' . APPLICATION_ENV
                . ' APPLICATION_STORE=' . APPLICATION_STORE
                . ' APPLICATION_ROOT_DIR=' . APPLICATION_ROOT_DIR
                . ' APPLICATION=' . APPLICATION
                . ' ' . $this->tourConfig->getPhpPathForConsole()
                . ' vendor/bin/console middleware:process:run'
                . ' -p ' . $processName
                . ' -o ' . $endpointUrl
                . ' -i ' . $idTour;
        }

        $this
            ->edifactLogger
            ->logNonEdi(
                $endpointUrl,
                200,
                sprintf(
                    'Trying to execute: %s',
                    $command
                ),
                DstEdifactExportLogTableMap::COL_LOG_LEVEL_INFO
            );

        $process = new Process(
            $command,
            APPLICATION_ROOT_DIR,
            null,
            null,
            $timeout
        );

        return $process
            ->run(function (

                /** @noinspection PhpUnusedParameterInspection */
                $type,
                $buffer
) {
                echo $buffer;
            });
    }

    /**
     * @param int $idTour
     * @param string $endpointUrl
     * @param int $timeout
     * @param bool $isGraphmastersTour
     *
     * @return int
     */
    public function ediExportDepositById(
        int $idTour,
        string $endpointUrl,
        int $timeout,
        bool $isGraphmastersTour = false
    ): int
    {
        $processName = $isGraphmastersTour === true
            ? GraphMastersDepositExportConfigurationPlugin::PROCESS_NAME
            : DepositExportConfigurationPlugin::PROCESS_NAME;

        $command = 'APPLICATION_ENV=' . APPLICATION_ENV
            . ' APPLICATION_STORE=' . APPLICATION_STORE
            . ' APPLICATION_ROOT_DIR=' . APPLICATION_ROOT_DIR
            . ' APPLICATION=' . APPLICATION
            . ' ' . $this->tourConfig->getPhpPathForConsole()
            . ' vendor/bin/console middleware:process:run'
            . ' -p ' . $processName
            . ' -o ' . $endpointUrl
            . ' -i ' . $idTour;

        $this
            ->edifactLogger
            ->logNonEdi(
                $endpointUrl,
                200,
                sprintf(
                    'Trying to execute: %s',
                    $command
                ),
                DstEdifactExportLogTableMap::COL_LOG_LEVEL_INFO
            );

        $process = new Process(
            $command,
            APPLICATION_ROOT_DIR,
            null,
            null,
            $timeout
        );

        return $process
            ->run(function(
                /** @noinspection PhpUnusedParameterInspection */
                $type,
                $buffer
            ) {
                echo $buffer;
            });
    }

}
