<?php
/**
 * Durst - project - EasybillException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.12.19
 * Time: 17:07
 */

namespace Pyz\Zed\Easybill\Business\Exception;

use RuntimeException;

class EasybillException extends RuntimeException
{
    protected const NO_NUMBER_IN_BODY = 'no number found in response body';
    protected const NON_SUCCESSFUL_RESPONSE_CODE = 'the response code was not successful. Code: %d';
    protected const INVALID_HTTP_VERB = 'invalid http verb %s';
    protected const INVALID_CUSTOMER = 'the customer is not valid. Please check the data.';
    protected const NO_BILLING_ADDRESS_IN_CUSTOMER = 'no billing address set in customer';
    protected const NO_ID_IN_BODY = 'there is no id set in the response body';
    protected const RESOURCE_NOT_FOUND = 'the resource with the given id %s was not found';

    /**
     * @return $this
     */
    public static function noNumberInBody(): self
    {
        return new EasybillException(static::NO_NUMBER_IN_BODY);
    }

    /**
     * @param int $code
     *
     * @return static
     */
    public static function nonSuccessfulResponseCode(int $code): self
    {
        return new EasybillException(
            sprintf(
                static::NON_SUCCESSFUL_RESPONSE_CODE,
                $code
            )
        );
    }

    /**
     * @param string $httpVerb
     *
     * @return static
     */
    public static function invalidHttpVerb(string $httpVerb): self
    {
        return new EasybillException(
            sprintf(
                static::INVALID_HTTP_VERB,
                $httpVerb
            )
        );
    }

    /**
     * @return static
     */
    public static function invalidCustomer(): self
    {
        return new EasybillException(
            static::INVALID_CUSTOMER
        );
    }

    /**
     * @return static
     */
    public static function noBillingAddressInCustomer(): self
    {
        return new EasybillException(
            static::NO_BILLING_ADDRESS_IN_CUSTOMER
        );
    }

    /**
     * @return static
     */
    public static function noIdInBody(): self
    {
        return new EasybillException(
            static::NO_ID_IN_BODY
        );
    }

    /**
     * @param int $id
     *
     * @return \Pyz\Zed\Easybill\Business\Exception\EasybillException
     */
    public static function resourceNotFound(int $id)
    {
        return new EasybillException(
            sprintf(
                static::RESOURCE_NOT_FOUND,
                $id
            )
        );
    }
}
