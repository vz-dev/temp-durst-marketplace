<?php
/**
 * Durst - project - GraphMastersSettingsInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 27.05.21
 * Time: 18:07
 */

namespace Pyz\Zed\GraphMasters\Business\Model;


use Generated\Shared\Transfer\GraphMastersSettingsTransfer;
use Propel\Runtime\Exception\PropelException;

interface GraphMastersSettingsInterface
{
    /**
     * @param GraphMastersSettingsTransfer $transfer
     */
    public function save(GraphMastersSettingsTransfer $transfer): void;

    /**
     * @param int $idSettings
     *
     * @return void
     */
    public function remove(int $idSettings): void;

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return bool
     */
    public function doesBranchUseGraphmasters(int $idBranch): bool;

    /**
     * @param int $idSettings
     * @param bool $withRelatedObjects
     *
     * @return GraphMastersSettingsTransfer
     *
     * @throws PropelException
     */
    public function getSettingsById(int $idSettings, bool $withRelatedObjects = false): GraphMastersSettingsTransfer;

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return GraphMastersSettingsTransfer
     */
    public function getSettingsByIdBranch(int $idBranch): GraphMastersSettingsTransfer;
}
