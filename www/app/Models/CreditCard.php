<?php

namespace App\Models;


/**
 * Class CreditCard represents a credit card.
 */
class CreditCard extends CreditCardNumberOnly
{
    const CARD_NAME   = 'card_name';
    const VALID_UNTIL = 'valid_until';
    const CCV         = 'ccv';

    /**
     * Credit card name.
     *
     * @var string
     */
    public $card_name;

    /**
     * Credit card valid until.
     *
     * @var string
     */
    public $valid_until;

    /**
     * Credit card ccv number.
     *
     * @var int
     */
    public $ccv;
}
