<?php

namespace Pyz\Zed\Integra\Business;

use Generated\Shared\Transfer\IntegraCredentialsTransfer;

interface IntegraFacadeInterface
{
    /**
     * Specification:
     *  - gathers information about all orders that
     *    * have flag "isExportable" set
     *    * match the given branch id
     *  - creates a csv file
     *  - exports the csv file to a remote location
     *
     * @param int $idBranch
     *
     * @return void
     */
    public function exportOpenOrdersForBranch(int $idBranch): void;

    /**
     * Specification:
     *  - gathers information about all orders that
     *    * have the flag "isClosable" set
     *    * match the given branch id
     *  - creates a csv file
     *  - exports the csv file to a remote location
     *
     * @param int $idBranch
     *
     * @return void
     */
    public function exportClosedOrdersForBranch(int $idBranch): void;

    /**
     * Specification:
     *  - Returns an array of branch ids that have
     *    * a configuration dataset
     *    * use integra flag set
     *    * ftp host
     *    * ftp user
     *    * ftp password
     *
     * @return array
     */
    public function getBranchIdsThatUseIntegra(): array;

    /**
     * Specification:
     *  Returns true only if
     *  - there is an integra config for the given branch
     *  - the config has useIntegra set to "true"
     *
     * @param int $idBranch
     *
     * @return bool
     */
    public function doesBranchUseIntegra(int $idBranch): bool;

    /**
     * Specification:
     *  - persists the transfer data to the db
     *  - if the property idIntegraCredentials is set, the entity will be updated
     *  - otherwise a new entity will be created
     *
     * @param IntegraCredentialsTransfer $transfer
     */
    public function save(IntegraCredentialsTransfer $transfer): void;

    /**
     * Specification:
     *  - removes the credentials with the given id
     *  - if no credentials with given id are found nothing happens
     *
     * @param int $idIntegraCredentials
     */
    public function removeCredentials(int $idIntegraCredentials): void;

    /**
     * Specification:
     *  - gets credentials for branch
     *  - executes tour query via web service
     *  - persists orders
     *
     * @param int $idBranch
     */
    public function importOrdersForBranch(int $idBranch): void;

    public function login(int $idBranch): string;

    /**
     * Get the INTEGRA credentials for the given branch
     *
     * @param int $idBranch
     * @return IntegraCredentialsTransfer
     */
    public function getCredentialsByIdBranch(int $idBranch): IntegraCredentialsTransfer;
}
