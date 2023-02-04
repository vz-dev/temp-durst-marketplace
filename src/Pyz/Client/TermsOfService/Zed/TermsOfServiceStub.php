<?php
/**
 * Durst - project - TermsOfServiceStub.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.05.18
 * Time: 11:56
 */

namespace Pyz\Client\TermsOfService\Zed;


use Generated\Shared\Transfer\TermsOfServiceTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;
use Spryker\Shared\Kernel\Transfer\TransferInterface;

class TermsOfServiceStub
{
    const URL_GET_CUSTOMER_TERMS = '/terms-of-service/gateway/get-customer-terms';

    /**
     * @var ZedRequestClientInterface
     */
    protected $zedStub;

    /**
     * TermsOfServiceStub constructor.
     * @param ZedRequestClientInterface $zedStub
     */
    public function __construct(ZedRequestClientInterface $zedStub)
    {
        $this->zedStub = $zedStub;
    }

    /**
     * @return TermsOfServiceTransfer|TransferInterface
     */
    public function getBranchesByZipCode() : TermsOfServiceTransfer
    {
        $dummyTransfer = new TermsOfServiceTransfer();

        return $this->zedStub->call(
            self::URL_GET_CUSTOMER_TERMS,
            $dummyTransfer,
            null
        );
    }

}