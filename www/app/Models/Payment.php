<?php

namespace App\Models;


/**
 * Class Payment represents a payment.
 */
class Payment
{
    const FROM         = 'from';
    const MERCHANT_ID  = 'card_number';
    const MERCHANT_KEY = 'merchant_key';
    const AMOUNT       = 'amount';

    /**
     * Payment from.
     *
     * @var CreditCard
     */
    public $from;

    /**
     * Merchant id.
     *
     * @var int
     */
    public $merchant_id;

    /**
     * Merchant key.
     *
     * @var string
     */
    public $merchant_key;

    /**
     * Amount to be paid.
     *
     * @var float
     */
    public $amount;
}
