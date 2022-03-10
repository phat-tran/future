<?php

namespace App\Models;


/**
 * Class CreditCardNumberOnly represents a credit card with only card number.
 */
class CreditCardNumberOnly
{
    const CARD_NUMBER = 'card_number';

    /**
     * Credit card number.
     *
     * @var int
     */
    public $card_number;
}
