<?php
/**
 * Durst - project - WebServiceManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.11.20
 * Time: 10:07
 */

namespace Pyz\Zed\Integra\Business\Model\Connection;

use Generated\Shared\Transfer\IntegraCredentialsTransfer;

interface WebServiceManagerInterface
{
    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     *
     * @return string
     */
    public function login(IntegraCredentialsTransfer $credentialsTransfer): string;

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @param string $sessionId
     */
    public function closeLogin(IntegraCredentialsTransfer $credentialsTransfer, string $sessionId): void;

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     *
     * @return array
     */
    public function importTours(IntegraCredentialsTransfer $credentialsTransfer): array;

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @param string $idCustomer
     *
     * @return array
     */
    public function getCustomer(IntegraCredentialsTransfer $credentialsTransfer, string $idCustomer): array;

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @param int $nrTourDelivery
     *
     * @return array
     */
    public function setImportedStatusForNrTourDelivery(IntegraCredentialsTransfer $credentialsTransfer, int $nrTourDelivery): array;

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @param string $nrKunde
     * @param string $deliveryStart
     * @param string $deliveryEnd
     * @param int $dayOfWeek
     *
     * @return array
     */
    public function addDeliveryTimesToCustomer(IntegraCredentialsTransfer $credentialsTransfer, string $nrKunde, string $deliveryStart, string $deliveryEnd, int $dayOfWeek): array;

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @return array
     */
    public function getProductToDeposit(IntegraCredentialsTransfer $credentialsTransfer): array;

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @return array
     */
    public function getProductMainUnitToSubUnit(IntegraCredentialsTransfer $credentialsTransfer): array;
}
