<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * Durst - Marketplace-Platform - RetailPaymentConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 02.03.18
 * Time: 12:04
 */

namespace Pyz\Shared\RetailPayment;


interface RetailPaymentConfig
{
    public const PROVIDER_NAME = 'RetailPayment';
    public const PAYMENT_METHOD_CASH = 'cash_on_delivery';
    public const PAYMENT_METHOD_ELV = 'elv';
    public const PAYMENT_METHOD_EC = 'ec_on_delivery';
    public const PAYMENT_METHOD_INVOICE = 'invoice';
    public const PAYMENT_METHOD_INVOICE_B2B = 'invoice_b2b';
    public const PAYMENT_METHOD_CREDIT_CARD = 'credit_card_on_delivery';
    public const PAYMENT_METHOD_DIRECT_DEBIT = 'direct_debit';
    public const PAYMENT_METHOD_WHOLESALE_CASH = 'cash_on_delivery_wholesale';
    public const PAYMENT_METHOD_WHOLESALE_EC = 'ec_on_delivery_wholesale';
    public const PAYMENT_METHOD_WHOLESALE_CREDIT_CARD = 'credit_card_on_delivery_wholesale';

    const PAYMENT_METHODS = [
        self::PAYMENT_METHOD_CASH,
        self::PAYMENT_METHOD_EC,
        self::PAYMENT_METHOD_ELV,
        self::PAYMENT_METHOD_CREDIT_CARD,
        self::PAYMENT_METHOD_DIRECT_DEBIT,
        self::PAYMENT_METHOD_INVOICE,
        self::PAYMENT_METHOD_INVOICE_B2B,
        self::PAYMENT_METHOD_WHOLESALE_CASH,
        self::PAYMENT_METHOD_WHOLESALE_EC,
        self::PAYMENT_METHOD_WHOLESALE_CREDIT_CARD,
    ];
}