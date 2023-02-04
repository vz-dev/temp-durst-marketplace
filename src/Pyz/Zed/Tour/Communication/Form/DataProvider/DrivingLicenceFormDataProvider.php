<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 02.08.18
 * Time: 12:59
 */

namespace Pyz\Zed\Tour\Communication\Form\DataProvider;


use Generated\Shared\Transfer\DrivingLicenceTransfer;
use Pyz\Zed\Tour\Business\TourFacadeInterface;

class DrivingLicenceFormDataProvider
{
    /**
     * @var TourFacadeInterface
     */
    protected $tourFacade;

    /**
     * VehicleFormDataProvider constructor.
     * @param TourFacadeInterface $vehicleFacade
     */
    public function __construct(TourFacadeInterface $vehicleFacade)
    {
        $this->tourFacade = $vehicleFacade;
    }

    /**
     * @return DrivingLicenceTransfer
     */
    public function getData(int $idDrivingLicence) : DrivingLicenceTransfer
    {
        return $this->tourFacade->getDrivingLicenceById($idDrivingLicence);
    }

    /**
     * @return array
     */
    public function getOptions() : array
    {
        return [];
    }

}
