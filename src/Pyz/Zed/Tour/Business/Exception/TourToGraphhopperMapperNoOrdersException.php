<?php
/**
 * Durst - project - TourToGraphhopperMapperNoOrdersException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-01-09
 * Time: 14:44
 */

namespace Pyz\Zed\Tour\Business\Exception;


use Exception;

class TourToGraphhopperMapperNoOrdersException extends Exception
{
    public const MESSAGE = 'There are no Orders for Tour %s';
}
