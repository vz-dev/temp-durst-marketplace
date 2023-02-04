<?php
/**
 * Durst - project - SalesConstants.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.08.18
 * Time: 14:02
 */

namespace Pyz\Shared\Sales;

use Spryker\Shared\Sales\SalesConstants as SprykerSalesConstants;

interface SalesConstants extends SprykerSalesConstants
{
    public const REFUND_EXPENSE_TYPE = 'REFUND_EXPENSE';
    public const REFUND_EXPENSE_DISPLAY_NAME = 'Retoure/Gutschein';
    public const COMMENT_TYPE_MERCHANT = 'COMMENT_MERCHANT';
    public const COMMENT_TYPE_CUSTOMER = 'COMMENT_CUSTOMER';
    public const COMMENT_TYPE_ADDRESS = 'COMMENT_ADDRESS';

    /**
     * Location of image files with signature of the customer to acknowledge the reception
     * of delivered goods
     */
    public const SALES_SIGNATURE_IMAGE_PATH = 'SALES_SIGNATURE_IMAGE_PATH';

    public const ORDER_ITEM_DELIVERY_STATUS_DELIVERED = 'delivered';
    public const ORDER_ITEM_DELIVERY_STATUS_DECLINED = 'declined';
    public const ORDER_ITEM_DELIVERY_STATUS_DAMAGED = 'damaged';
    public const ORDER_ITEM_DELIVERY_STATUS_MISSING = 'missing';
    public const ORDER_ITEM_DELIVERY_STATUS_CANCELLED = 'cancelled';
}
