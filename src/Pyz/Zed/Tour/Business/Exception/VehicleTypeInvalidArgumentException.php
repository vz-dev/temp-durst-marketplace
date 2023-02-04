<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 04.09.18
 * Time: 11:37
 */

namespace Pyz\Zed\Tour\Business\Exception;


class VehicleTypeInvalidArgumentException extends VehicleTypeException
{
    public const MESSAGE = 'Vehicle type transfer object is invalid';
    public const NO_FK_BRANCH_MESSAGE = 'The fkBranch of a vehicle type must not be null';

}
