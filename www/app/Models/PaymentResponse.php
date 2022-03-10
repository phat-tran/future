<?php

namespace App\Models;


/**
 * Class PaymentResponse represents a payment response.
 */
class PaymentResponse
{
    const FROM               = 'from';
    const TRANSACTION_NUMBER = 'transaction_number';
    const TRANSACTION_TIME   = 'transaction_time';
    const AMOUNT             = 'amount';

    /**
     * Payment from.
     *
     * @var CreditCardNumberOnly
     */
    public $from;

    /**
     * Transaction number.
     *
     * @var int
     */
    public $transaction_number;

    /**
     * Transaction time.
     *
     * @var string
     */
    public $transaction_time;

    /**
     * Amount paid.
     *
     * @var float
     */
    public $amount;
}
