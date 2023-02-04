<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 13.12.18
 * Time: 08:12
 */

namespace Pyz\Zed\Tour\Business\Exception;


class ConcreteTourInvalidArgumentException extends ConcreteTourException
{
    public const NO_BRANCH_MESSAGE = 'The fkBranch of a concrete tour cannot be null.';
    public const NOT_IN_CURRENT_BRANCH = 'The concrete Tour is not in current branch.';
}
