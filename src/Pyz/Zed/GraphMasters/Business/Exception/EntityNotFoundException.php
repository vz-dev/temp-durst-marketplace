<?php
/**
 * Durst - project - EntiyNotFoundException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 27.05.21
 * Time: 18:30
 */

namespace Pyz\Zed\GraphMasters\Business\Exception;


use RuntimeException;

class EntityNotFoundException extends RuntimeException
{
    protected const MESSAGE = 'entity with id %d not found';
    protected const BRANCH = 'entity for branch #%d not found';
    protected const REFERENCE = 'entity with reference %d not found';

    /**
     * @param int $idEntity
     *
     * @return static
     */
    public static function build(int $idEntity): self
    {
        return new EntityNotFoundException(
            sprintf(
                static::MESSAGE,
                $idEntity
            )
        );
    }

    /**
     * @param int $idBranch
     *
     * @return static
     */
    public static function branch(int $idBranch): self
    {
        return new EntityNotFoundException(
            sprintf(
                static::BRANCH,
                $idBranch
            )
        );
    }

    /**
     * @param string $reference
     *
     * @return static
     */
    public static function reference(string $reference): self
    {
        return new EntityNotFoundException(
            sprintf(
                static::REFERENCE,
                $reference
            )
        );
    }
}
