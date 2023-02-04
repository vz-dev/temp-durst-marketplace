<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 25.10.18
 * Time: 16:41
 */

namespace Pyz\Zed\Tour\Business\Exception;


class ConcreteTourExistsException extends ConcreteTourException
{
    public const ID_EXISTS_MESSAGE = 'A concrete tour with the id "%d" already exists';

}
