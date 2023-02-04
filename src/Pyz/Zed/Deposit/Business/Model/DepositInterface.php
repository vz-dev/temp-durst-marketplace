<?php
/**
 * Durst - project - DepositInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.09.18
 * Time: 09:23
 */

namespace Pyz\Zed\Deposit\Business\Model;

use Generated\Shared\Transfer\DepositTransfer;

interface DepositInterface
{
    /**
     * @param \Generated\Shared\Transfer\DepositTransfer $depositTransfer
     *
     * @throws \Pyz\Zed\Deposit\Business\Exception\DepositExistsException if a deposit with the given name already exists
     * @throws \Pyz\Zed\Deposit\Business\Exception\DepositInvalidArgumentException if the given name is null
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function save(DepositTransfer $depositTransfer);

    /**
     * @param int $idDeposit
     *
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function remove($idDeposit);

    /**
     * @return \Generated\Shared\Transfer\DepositTransfer[]
     */
    public function getDeposits();

    /**
     * @param int $idDeposit
     *
     * @throws \Pyz\Zed\Deposit\Business\Exception\DepositNotFoundException if there is no deposit with the given id in the database
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function getDepositById($idDeposit);

    /**
     * @param \Generated\Shared\Transfer\DepositTransfer $depositTransfer
     *
     * @throws \Pyz\Zed\Deposit\Business\Exception\DepositExistsException
     * @throws \Pyz\Zed\Deposit\Business\Exception\DepositInvalidArgumentException
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function addDeposit(DepositTransfer $depositTransfer);

    /**
     * @param int $idDeposit
     *
     * @return bool
     */
    public function hasDeposit($idDeposit);

    /**
     * @return bool
     */
    public function depositsAreImported();

    /**
     * @param string $sku
     *
     * @throws \Pyz\Zed\Deposit\Business\Exception\DepositMissingException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function getDepositForProductBySku(string $sku): DepositTransfer;
}
