<?php
/**
 * Durst - project - CancelOrderFacadeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 16:48
 */

namespace Pyz\Zed\CancelOrder\Business;

use DateTime;
use Generated\Shared\Transfer\CancelOrderTransfer;
use Generated\Shared\Transfer\JwtTransfer;

/**
 * Interface CancelOrderFacadeInterface
 * @package Pyz\Zed\CancelOrder\Business
 */
interface CancelOrderFacadeInterface
{
    /**
     * Generate a JWT transfer for the given sales order ID
     * It is possible to also give an explicit expiration date for the token
     * The issuer is taken from the sales order itself
     *
     * @param int $idSalesOrder
     * @param \DateTime|null $manualExpireDate
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function generateToken(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer;

    /**
     * Generate a JWT transfer for the given sales order ID and the given issuer
     *
     * @param int $idSalesOrder
     * @param string|null $issuer
     * @param \DateTime|null $manualExpireDate
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function generateTokenForIssuer(
        int $idSalesOrder,
        ?string $issuer = null,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer;

    /**
     * Parse the token and create a JWT transfer from it
     * ATTENTION: sign is missing, can't be fetched from token
     *
     * @param string $token
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function getJwtFromToken(
        string $token
    ): JwtTransfer;

    /**
     * Check for valid token:
     * - Token is expired check
     * - Token is not valid check (claims)
     * - Sign is not the same between token and email from sales order
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return bool
     */
    public function isValid(
        JwtTransfer $jwtTransfer
    ): bool;

    /**
     * Uses the same checks as @see CancelOrderFacadeInterface::isValid()
     * Throw the exception which is hidden behind true/false of said function
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return void
     */
    public function checkTransfer(
        JwtTransfer $jwtTransfer
    ): void;

    /**
     * Executes the:
     * - basic validators
     * - additional validators
     * from the given JWT transfer and save the errors in the transfer
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function executeJwtValidators(
        JwtTransfer $jwtTransfer
    ): JwtTransfer;

    /**
     * Check, if the given sign was used to sign the given token
     *
     * @param string|null $token
     * @param string|null $sign
     * @return bool
     */
    public function verifySignByToken(
        ?string $token = null,
        ?string $sign = null
    ): bool;

    /**
     * Persist the canceled order by the given token
     *
     * @param string $token
     * @return bool
     */
    public function saveCancelOrder(
        string $token
    ): bool;

    /**
     * Get a saved cancel order by its ID
     *
     * @param int $idCancelOrder
     * @return \Generated\Shared\Transfer\CancelOrderTransfer
     */
    public function getCancelOrderById(
        int $idCancelOrder
    ): CancelOrderTransfer;

    /**
     * Try and get the cancel order for the given sales order ID
     *
     * @param int $idSalesOrder
     * @return \Generated\Shared\Transfer\CancelOrderTransfer|null
     */
    public function getCancelOrderByIdSalesOrder(
        int $idSalesOrder
    ): ?CancelOrderTransfer;

    /**
     * Try to generate a token for the given sales order ID
     * The issuer is set to customer by default
     *
     * @param int $idSalesOrder
     * @param \DateTime|null $manualExpireDate
     * @return string|null
     */
    public function getTokenForCustomerMail(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): ?string;

    /**
     * Try to generate a JWT transfer for the given sales order ID
     * The issuer is set to fridge by default
     * The JWT transfer may contain error messages for the user to display
     *
     * @param int $idSalesOrder
     * @param \DateTime|null $manualExpireDate
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function getJwtTransferForFridge(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer;

    /**
     * Try to generate a JWT transfer for the given sales order ID
     * The issuer is set to driver by default
     * The JWT transfer may contain error messages for the driver to display
     *
     * @param int $idSalesOrder
     * @param \DateTime|null $manualExpireDate
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function getJwtTransferForDriver(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer;

    /**
     * Get the token and prepare everything for triggering the OMS event
     * to cancel the order:
     * - Update sales order (issuer)
     * - Update sales order (message)
     * - Update sales order (driver)
     * - Validation of token
     *
     * @param string|null $token
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function prepareTriggerFromToken(
        ?string $token = null
    ): JwtTransfer;
}
