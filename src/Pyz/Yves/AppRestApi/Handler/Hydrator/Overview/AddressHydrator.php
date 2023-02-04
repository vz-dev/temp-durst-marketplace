<?php
/**
 * Durst - project - AddressHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 14.01.21
 * Time: 09:49
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Overview;


use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\OverviewKeyRequestInterface;
use stdClass;

class AddressHydrator implements HydratorInterface
{
    protected const KEY_SHIPPING_ADDRESS = 'shipping_address';

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        $shipping = new stdClass();

        $shipping
            ->{OverviewKeyRequestInterface::KEY_ADDRESS_ZIP_CODE} = null;

        if (
            isset($requestObject->{static::KEY_SHIPPING_ADDRESS}) === true &&
            isset($requestObject->{static::KEY_SHIPPING_ADDRESS}->{OverviewKeyRequestInterface::KEY_ADDRESS_ZIP_CODE}) === true
        ) {
            $shipping->{OverviewKeyRequestInterface::KEY_ADDRESS_ZIP_CODE} = $requestObject
                ->{static::KEY_SHIPPING_ADDRESS}
                ->{OverviewKeyRequestInterface::KEY_ADDRESS_ZIP_CODE};
        }

        $requestObject->{OverviewKeyRequestInterface::KEY_SHIPPING_ADDRESS} = $shipping;
    }
}
