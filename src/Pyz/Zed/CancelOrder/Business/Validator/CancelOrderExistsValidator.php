<?php
/**
 * Durst - project - CancelOrderExistsValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 07.09.21
 * Time: 16:03
 */

namespace Pyz\Zed\CancelOrder\Business\Validator;

use Generated\Shared\Transfer\JwtTransfer;
use Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface;
use Pyz\Zed\CancelOrder\Business\Exception\CancelOrderAlreadyExistsException;
use Pyz\Zed\Jwt\Business\Validator\BaseJwtValidator;

/**
 * Class CancelOrderExistsValidator
 * @package Pyz\Zed\CancelOrder\Business\Validator
 */
class CancelOrderExistsValidator extends BaseJwtValidator
{
    protected const ERROR_CODE = '200001';
    protected const ERROR_MESSAGE = 'Diese Bestellung wurde bereits storniert.';

    /**
     * @var \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface
     */
    protected $facade;

    /**
     * @param \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface $facade
     */
    public function __construct(
        CancelOrderFacadeInterface $facade
    )
    {
        $this->facade = $facade;
    }

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return bool
     * @throws \Pyz\Zed\CancelOrder\Business\Exception\CancelOrderAlreadyExistsException
     */
    public function isValid(
        JwtTransfer $jwtTransfer
    ): bool
    {
        $cancelOrder = $this
            ->facade
            ->getCancelOrderByIdSalesOrder(
                $jwtTransfer
                    ->getId()
            );

        if ($cancelOrder !== null) {
            throw new CancelOrderAlreadyExistsException(
                CancelOrderAlreadyExistsException::MESSAGE
            );
        }

        return true;
    }
}
