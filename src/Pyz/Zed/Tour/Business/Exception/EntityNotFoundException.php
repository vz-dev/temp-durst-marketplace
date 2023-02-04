<?php
/**
 * Durst - project - EntityNotFoundException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 04.11.19
 * Time: 14:08
 */

namespace Pyz\Zed\Tour\Business\Exception;


class EntityNotFoundException extends \RuntimeException
{
    const MESSAGE_CONCRETE_TOUR_EXPORT = 'concrete tour export for tour with id %d not found';

    /**
     * @param int $idConcreteTour
     * @return static
     */
    public static function concreteTourExport(int $idConcreteTour): self {
        return new self(
            sprintf(
                self::MESSAGE_CONCRETE_TOUR_EXPORT,
                $idConcreteTour
            )
        );
    }
}
