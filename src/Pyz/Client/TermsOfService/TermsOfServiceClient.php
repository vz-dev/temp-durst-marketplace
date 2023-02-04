<?php
/**
 * Durst - project - TermsOfServiceClient.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.05.18
 * Time: 11:56
 */

namespace Pyz\Client\TermsOfService;


use Generated\Shared\Transfer\TermsOfServiceTransfer;
use Spryker\Client\Kernel\AbstractClient;
use Spryker\Shared\Kernel\Transfer\TransferInterface;

/**
 * Class TermsOfServiceClient
 * @package Pyz\Client\TermsOfService
 * @method TermsOfServiceFactory getFactory()
 */
class TermsOfServiceClient extends AbstractClient implements TermsOfServiceClientInterface
{
    /**
     * {@inheritdoc}
     *
     * @return TermsOfServiceTransfer|TransferInterface
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCustomerTerms(): TermsOfServiceTransfer
    {
        return $this
            ->getFactory()
            ->createTermsOfServiceStub()
            ->getBranchesByZipCode();
    }
}