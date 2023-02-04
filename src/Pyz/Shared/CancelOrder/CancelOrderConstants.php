<?php
/**
 * Durst - project - CancelOrderConstants.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 16:43
 */

namespace Pyz\Shared\CancelOrder;

/**
 * Interface CancelOrderConstants
 * @package Pyz\Shared\CancelOrder
 */
interface CancelOrderConstants
{
    public const KEY_ID_BILLING = 'idBilling';
    public const KEY_ID_SHIPPING = 'idShipping';
    public const KEY_ID_CONCRETE_TOUR = 'idConcreteTour';
    public const KEY_ID_DRIVER = 'idDriver';
    public const KEY_TOUR_START = 'tourStart';
    public const KEY_MESSAGE = 'cancelMessage';

    public const CANCEL_LEAD_TIME = 'CANCEL_LEAD_TIME';

    public const FRIDGE_CANCEL_URL = 'FRIDGE_CANCEL_URL';

    public const POSSIBLE_ISSUERS = 'POSSIBLE_ISSUERS';

    public const ISSUER_FRIDGE = 'ISSUER_FRIDGE';
    public const ISSUER_CUSTOMER = 'ISSUER_CUSTOMER';
    public const ISSUER_DRIVER = 'ISSUER_DRIVER';

    public const FRIDGE = 'fridge';
    public const CUSTOMER = 'customer';
    public const DRIVER = 'driver';

    public const CANCEL_MESSAGE_NOT_AT_HOME = 'CANCEL_MESSAGE_NOT_AT_HOME';
    public const CANCEL_MESSAGE_NOT_ACCEPTED = 'CANCEL_MESSAGE_NOT_ACCEPTED';
}
