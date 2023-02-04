<?php
/**
 * Durst - project - DiscountStub.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.09.20
 * Time: 14:37
 */

namespace Pyz\Client\Discount\Zed;


use Generated\Shared\Transfer\DiscountApiRequestTransfer;
use Generated\Shared\Transfer\DiscountApiResponseTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class DiscountStub implements DiscountStubInterface
{
    protected const URL_DISCOUNT_CHECK_VOUCHER = '/discount/gateway/check-valid-voucher';

    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected $zedStub;

    /**
     * DiscountStub constructor.
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedStub
     */
    public function __construct(
        ZedRequestClientInterface $zedStub
    )
    {
        $this->zedStub = $zedStub;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DiscountApiRequestTransfer $discountApiRequestTransfer
     * @return \Generated\Shared\Transfer\DiscountApiResponseTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function checkValidVoucher(
        DiscountApiRequestTransfer $discountApiRequestTransfer
    ): DiscountApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                static::URL_DISCOUNT_CHECK_VOUCHER,
                $discountApiRequestTransfer,
                null
            );
    }
}
