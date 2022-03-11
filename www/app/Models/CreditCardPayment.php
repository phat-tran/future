<?php

namespace App\Models;


/**
 * Class CreditCardPayment represents a credit card with payment.
 */
class CreditCardPayment extends CreditCard
{
    /**
     * The amount to pay.
     *
     * @var float
     */
    public $amount;
}
