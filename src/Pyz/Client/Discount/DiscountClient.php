<?php
/**
 * Durst - project - DiscountClient.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.09.20
 * Time: 13:56
 */

namespace Pyz\Client\Discount;


use Generated\Shared\Transfer\DiscountApiRequestTransfer;
use Generated\Shared\Transfer\DiscountApiResponseTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * Class DiscountClient
 * @package Pyz\Client\Discount
 * @method \Pyz\Client\Discount\DiscountFactory getFactory()
 */
class DiscountClient extends AbstractClient implements DiscountClientInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DiscountApiRequestTransfer $discountApiRequestTransfer
     * @return \Generated\Shared\Transfer\DiscountApiResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function checkValidVoucher(
        DiscountApiRequestTransfer $discountApiRequestTransfer
    ): DiscountApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createDiscountStub()
            ->checkValidVoucher(
                $discountApiRequestTransfer
            );
    }

}
