<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 26.10.18
 * Time: 07:38
 */

namespace Pyz\Zed\Tour\Business\Exception;


class ConcreteTourNotExistsException extends ConcreteTourException
{
    public const ID_NOT_EXISTS_MESSAGE = 'A concrete tour with the id "%d" does not exist.';
    public const TOUR_REFERENCE_NOT_EXISTS_MESSAGE = 'A concrete tour with the reference "%s" does not exist.';

    /**
     * @param int $idConcreteTour
     * @return static
     */
    public static function doesntExistWithId(int $idConcreteTour): self {
        return new ConcreteTourNotExistsException(
            sprintf(
                self::ID_NOT_EXISTS_MESSAGE,
                $idConcreteTour
            )
        );
    }
}
