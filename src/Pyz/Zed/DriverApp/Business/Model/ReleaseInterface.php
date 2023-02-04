<?php
/**
 * Durst - project - ReleaseInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-06
 * Time: 10:16
 */

namespace Pyz\Zed\DriverApp\Business\Model;

use Generated\Shared\Transfer\DriverAppReleaseTransfer;

interface ReleaseInterface
{
    /**
     * @param \Generated\Shared\Transfer\DriverAppReleaseTransfer $releaseTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function addRelease(DriverAppReleaseTransfer $releaseTransfer): DriverAppReleaseTransfer;

    /**
     * @param int $idDriverAppRelease
     *
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function deleteRelease(int $idDriverAppRelease): void;

    /**
     * @param int $idDriverAppRelease
     *
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function getReleaseById(int $idDriverAppRelease): DriverAppReleaseTransfer;

    /**
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function getLatestRelease(): DriverAppReleaseTransfer;

    /**
     * @param String $currentVersion
     *
     * @return bool
     */
    public function isUpdatable(String $currentVersion): bool;
}
