<?php
/**
 * Durst - project - EntityNotFoundException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.11.20
 * Time: 10:44
 */

namespace Pyz\Zed\Integra\Business\Exception;

use RuntimeException;

class EntityNotFoundException extends RuntimeException
{
    protected const MESSAGE = 'entity with id %d not found';
    protected const BRANCH = 'entity for branch #%d not found';
    protected const PRODUCT = 'product with merchant sku %s not in merchants range of goods';
    protected const DEPOSIT = 'deposit for product with sku %s not found';

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
     * @param string $merchantSku
     *
     * @return static
     */
    public static function product(string $merchantSku): self
    {
        return new EntityNotFoundException(
            sprintf(
                static::PRODUCT,
                $merchantSku
            )
        );
    }

    /**
     * @param string $sku
     *
     * @return static
     */
    public static function deposit(string $sku): self
    {
        return new EntityNotFoundException(
            sprintf(
                static::DEPOSIT,
                $sku
            )
        );
    }
}
