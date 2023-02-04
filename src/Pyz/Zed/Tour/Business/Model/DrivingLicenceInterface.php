<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 21.08.18
 * Time: 10:38
 */

namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\DrivingLicenceTransfer;

interface DrivingLicenceInterface
{
    /**
     * @return array
     */
    public function getDrivingLicences() : array;

    /**
     * @param int $idDrivingLicence
     * @return DrivingLicenceTransfer
     */
    public function getDrivingLicenceById(int $idDrivingLicence) : DrivingLicenceTransfer;

    /**
     * @param string $code
     * @return bool
     */
    public function drivingLicenceWithCodeExists(string $code) : bool;

    /**
     * @param DrivingLicenceTransfer $drivingLicenceTransfer
     * @return DrivingLicenceTransfer
     */
    public function save(DrivingLicenceTransfer $drivingLicenceTransfer) : DrivingLicenceTransfer;

    /**
     * @param int $idDrivingLicence
     * @return mixed
     */
    public function removeById(int $idDrivingLicence);

}
