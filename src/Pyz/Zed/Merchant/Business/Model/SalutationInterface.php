<?php
/**
 * Durst - project - SalutationInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 13:11
 */

namespace Pyz\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\SalutationTransfer;

interface SalutationInterface
{
    /**
     * @param \Generated\Shared\Transfer\SalutationTransfer $salutationTransfer
     * @return \Generated\Shared\Transfer\SalutationTransfer
     */
    public function add(SalutationTransfer $salutationTransfer): SalutationTransfer;

    /**
     * @param \Generated\Shared\Transfer\SalutationTransfer $salutationTransfer
     * @return \Generated\Shared\Transfer\SalutationTransfer
     */
    public function save(SalutationTransfer $salutationTransfer): SalutationTransfer;

    /**
     * @param $idSalutation
     * @return void
     */
    public function delete($idSalutation): void;

    /**
     * @param int $idSalutation
     * @return \Generated\Shared\Transfer\SalutationTransfer
     */
    public function getSalutationById(int $idSalutation): SalutationTransfer;

    /**
     * @return bool
     */
    public function enumSalutationsAreImported(): bool;
}
