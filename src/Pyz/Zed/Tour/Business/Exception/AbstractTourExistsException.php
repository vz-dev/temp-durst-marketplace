<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 13.09.18
 * Time: 12:57
 */

namespace Pyz\Zed\Tour\Business\Exception;


class AbstractTourExistsException extends AbstractTourException
{
    public const ID_EXISTS_MESSAGE = 'An abstract tour with the id "%d" already exists';

}
