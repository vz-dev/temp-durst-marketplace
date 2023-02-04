<?php
/**
 * Durst - project - GoogleApiFacade.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-09
 * Time: 07:39
 */

namespace Pyz\Zed\GoogleApi\Business;


use Generated\Shared\Transfer\GoogleApiCoordinatesTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * Class GoogleApiFacade
 * @package Pyz\Zed\GoogleApi\Business
 * @method GoogleApiBusinessFactory getFactory()
 */
class GoogleApiFacade extends AbstractFacade implements GoogleApiFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param string $queryString
     * @param string|null $postcode
     * @return GoogleApiCoordinatesTransfer
     * @throws ContainerKeyNotFoundException
     */
    public function getCoordinatesForAddressString(string $queryString, ?string $postcode=null): GoogleApiCoordinatesTransfer
    {
        return $this
            ->getFactory()
            ->createGeocoder()
            ->getCoordinatesForAddressString($queryString, $postcode);
    }

    /**
     * {@inheritDoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @throws PropelException
     * @throws ContainerKeyNotFoundException
     * @throws AmbiguousComparisonException
     */
    public function saveLatLngInOrderAddress(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer){
        $this
            ->getFactory()
            ->createLatLngAddressSaver()
            ->saveLatLngToAddress($quoteTransfer, $saveOrderTransfer);
    }
}
