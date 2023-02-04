<?php
/**
 * Durst - project - HeidelpayConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 15.01.19
 * Time: 14:50
 */

namespace Pyz\Shared\HeidelpayRest;


interface HeidelpayRestConstants
{
    /**
     * Access information provided by heidelpay
     */
    public const HEIDELPAY_REST_PRIVATE_KEY = 'HEIDELPAY_REST_PRIVATE_KEY';
    public const HEIDELPAY_REST_PUBLIC_KEY = 'HEIDELPAY_REST_PUBLIC_KEY';

    /**
     * Version of the REST API
     */
    public const HEIDELPAY_REST_API_VERSION = 'HEIDELPAY_REST_API_VERSION';

    /**
     * Information needed to open a connection to the REST Api
     */
    public const HEIDELPAY_REST_HOST = 'HEIDELPAY_REST_HOST';
    public const HEIDELPAY_BASE_URL = 'HEIDELPAY_BASE_URL';

    /**
     * Locale is needed to initialize the heidelpay client object.
     * String following Posix standard e.g. en_US
     */
    public const HEIDELPAY_LOCALE = 'HEIDELPAY_LOCALE';

    /**
     * Url that is passed to heidelpay upon authorizing a payment. If the user needs to be redirected to a url
     * where he/she needs to further authorize the payment, heidelpay will redirect the user to this url
     * after the authorization (successful or nor)
     */
    public const HEIDELPAY_RETURN_URL = 'HEIDELPAY_RETURN_URL';

    /**
     * Url to the sepa mandate, the user needs to accept if he/she wants to pay sepa direct debit
     */
    public const HEIDELPAY_REST_SEPA_MANDATE_URL = 'HEIDELPAY_REST_SEPA_MANDATE_URL';

    /**
     * Under certain circumstances heidelpay won't return a error message for the client. If that happens
     * this message will be returned instead.
     */
    public const HEIDELPAY_REST_FALLBACK_ERROR_MESSAGE = 'HEIDELPAY_REST_FALLBACK_ERROR_MESSAGE';

    /**
     * Constants for name of payment provider and all payment methods
     */
    public const HEIDELPAY_REST_PAYMENT_PROVIDER = 'HeidelpayRest';
    public const HEIDELPAY_REST_PAYMENT_METHOD_CREDIT_CARD_AUTHORIZE = self::HEIDELPAY_REST_PAYMENT_PROVIDER . 'CreditCardAuthorize';
    public const HEIDELPAY_REST_PAYMENT_METHOD_PAY_PAL_AUTHORIZE = self::HEIDELPAY_REST_PAYMENT_PROVIDER . 'PayPalAuthorize';
    public const HEIDELPAY_REST_PAYMENT_METHOD_PAY_PAL_DEBIT = self::HEIDELPAY_REST_PAYMENT_PROVIDER . 'PayPalDebit';
    public const HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT = self::HEIDELPAY_REST_PAYMENT_PROVIDER . 'SepaDirectDebit';
    public const HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT_B2B = self::HEIDELPAY_REST_PAYMENT_PROVIDER . 'SepaDirectDebitB2B';
    public const HEIDELPAY_REST_PAYMENT_METHOD_INVOICE = self::HEIDELPAY_REST_PAYMENT_PROVIDER . 'Invoice';
    public const HEIDELPAY_REST_PAYMENT_METHOD_INVOICE_GUARANTEED = self::HEIDELPAY_REST_PAYMENT_PROVIDER . 'InvoiceGuaranteed';
    public const HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT_GUARANTEED = self::HEIDELPAY_REST_PAYMENT_PROVIDER . 'SepaDirectDebitGuaranteed';
    public const HEIDELPAY_REST_PAYMENT_METHOD_CASH_ON_DELIVERY = self::HEIDELPAY_REST_PAYMENT_PROVIDER . 'CashOnDelivery';
    public const HEIDELPAY_REST_PAYMENT_METHOD_EC_CARD_ON_DELIVERY = self::HEIDELPAY_REST_PAYMENT_PROVIDER . 'EcCardOnDelivery';
    public const HEIDELPAY_REST_PAYMENT_METHOD_CREDIT_CARD_ON_DELIVERY = self::HEIDELPAY_REST_PAYMENT_PROVIDER . 'CreditCardOnDelivery';

    /**
     * All payment methods that are supported by this payment provider
     */
    public const HEIDELPAY_REST_PAYMENT_METHODS = [
        self::HEIDELPAY_REST_PAYMENT_METHOD_CREDIT_CARD_AUTHORIZE,
        self::HEIDELPAY_REST_PAYMENT_METHOD_PAY_PAL_AUTHORIZE,
        self::HEIDELPAY_REST_PAYMENT_METHOD_PAY_PAL_DEBIT,
        self::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT,
        self::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT_B2B,
        self::HEIDELPAY_REST_PAYMENT_METHOD_INVOICE,
        self::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT_GUARANTEED,
        self::HEIDELPAY_REST_PAYMENT_METHOD_INVOICE_GUARANTEED,
        self::HEIDELPAY_REST_PAYMENT_METHOD_CASH_ON_DELIVERY,
        self::HEIDELPAY_REST_PAYMENT_METHOD_EC_CARD_ON_DELIVERY,
        self::HEIDELPAY_REST_PAYMENT_METHOD_CREDIT_CARD_ON_DELIVERY,
    ];

    /**
     * Constants to identify transactions.
     */
    public const HEIDELPAY_REST_TRANSACTION_AUTHORIZE = 'TRANSACTION_AUTHORIZE';
    public const HEIDELPAY_REST_TRANSACTION_AUTHORIZE_CHECK = 'TRANSACTION_AUTHORIZE_CHECK';
    public const HEIDELPAY_REST_TRANSACTION_CANCELLATION = 'TRANSACTION_CANCELLATION';
    public const HEIDELPAY_REST_TRANSACTION_CHARGE = 'TRANSACTION_CHARGE';
    public const HEIDELPAY_REST_TRANSACTION_FINALIZE = 'TRANSACTION_FINALIZE';
    public const HEIDELPAY_REST_TRANSACTION_REFUND = 'TRANSACTION_REFUND';
    public const HEIDELPAY_REST_TRANSACTION_CREATE = 'TRANSACTION_CREATE';
    public const HEIDELPAY_REST_TRANSACTION_FETCH = 'TRANSACTION_FETCH';

    /**
     * Constants to identify transaction statuses
     */
    public const HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS = 'success';
    public const HEIDELPAY_REST_TRANSACTION_STATUS_PENDING = 'pending';
    public const HEIDELPAY_REST_TRANSACTION_STATUS_ERROR = 'error';

    public const HEIDELPAY_REST_INVOICE_KEY_IBAN = 'iban';
    public const HEIDELPAY_REST_INVOICE_KEY_BIC = 'bic';
    public const HEIDELPAY_REST_INVOICE_KEY_HOLDER = 'holder';
    public const HEIDELPAY_REST_INVOICE_KEY_DATE_OF_PAYMENT = 'date of payment';
    public const HEIDELPAY_REST_INVOICE_KEY_DESCRIPTOR = 'descriptor';

    /**
     * Manages whether or not the generated HTTP requests and responses should be logged by the debug handler
     */
    public const HEIDELPAY_REST_IS_DEBUG = 'HEIDELPAY_IS_DEBUG';

    /**
     * The path to the file the debug handler should log to
     */
    public const HEIDELPAY_REST_DEBUG_LOG_PATH = 'HEIDELPAY_REST_DEBUG_LOG_PATH';

    //TODO move to currency
    public const HEIDELPAY_REST_CURRENCY_EUR = 'EUR';

    /**
     * Date after which branch independent heidelpay keys should be used for all HP transactions(authorize, customer, payment, etc.)
     */
    public const HEIDELPAY_REST_START_DATE_BRANCH_SPECIFIC_KEYS = 'HEIDELPAY_REST_DEADLINE_BRANCH_INDEPENDENT_KEYS';

    public const HEIDELPAY_REST_PAYMENT_TYPE_MAP = 'HEIDELPAY_REST_PAYMENT_TYPE_MAP';

    /**
     * Errors / Exceptions we might solve with a retry, e.g.
     * - core timeout
     */
    public const HEIDELPAY_REST_RECOVERABLE_ERRORS = 'HEIDELPAY_REST_RECOVERABLE_ERRORS';

    /**
     * Codes from the database, used in mail template to switch between different text blocks
     * @see: database, table spy_payment_method
     */
    public const TWIG_HEIDELPAYRESTPAYPALAUTHORIZE = 'Paypal';
    public const TWIG_HEIDELPAYRESTCREDITCARDAUTHORIZE = 'Kreditkarte';
    public const TWIG_HEIDELPAYRESTSEPADIRECTDEBIT = 'SEPA-Lastschrift';
    public const TWIG_HEIDELPAYRESTSEPADIRECTDEBITB2B = 'SEPA für Firmenkunden';
    public const TWIG_HEIDELPAYRESTSEPADIRECTDEBITGUARANTEED = 'SEPA-Lastschrift garantiert';
    public const TWIG_HEIDELPAYRESTINVOICE = 'Rechnungskauf';
    public const TWIG_HEIDELPAYRESTINVOICEGUARANTEED = 'Rechnungskauf (garantiert)';
    public const TWIG_CASH_ON_DELIVERY = 'Barzahlung bei Lieferung';
    public const TWIG_EC_ON_DELIVERY = 'EC-Zahlung bei Lieferung';
    public const TWIG_CREDIT_CARD_ON_DELIVERY = 'Kreditkarte bei Lieferung';
    public const TWIG_INVOICE_B2B = 'Rechnung Firmenkunden';
}
