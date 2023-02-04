<?php
/**
 * Durst - project - EdiExportManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.01.19
 * Time: 09:38
 */

namespace Pyz\Zed\Tour\Business\Manager;

interface EdiExportManagerInterface
{
    /**
     * @param int $idTour
     * @param string $endpointUrl
     * @param int $timeout
     * @param bool $isGraphmastersTour
     *
     * @return int
     */
    public function ediExportTourById(
        int $idTour,
        string $endpointUrl,
        int $timeout,
        bool $isGraphmastersTour = false
    ): int;

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
    ): int;
}
