<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 13.09.18
 * Time: 12:58
 */

namespace Pyz\Zed\Tour\Business\Exception;


class AbstractTourInvalidArgumentException extends AbstractTourException
{
    public const NO_VEHICLE_CLASS_MESSAGE = 'The vehicle class of an abstract tour cannot be null.';
    public const NO_WEEKDAY_MESSAGE = 'The weekday of an abstract tour cannot be null.';
    public const NO_BRANCH_MESSAGE = 'The fkBranch of an abstract tour cannot be null.';
    public const NOT_IN_CURRENT_BRANCH = 'The abstract Tour is not in current branch.';

}
