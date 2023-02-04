<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 05.10.18
 * Time: 14:40
 */

namespace Pyz\Zed\Tour\Business\Exception;


class AbstractTourNotExistsException extends AbstractTourException
{
    public const ID_NOT_EXISTS_MESSAGE = 'An abstract tour with the id "%d" does not exist.';
}
