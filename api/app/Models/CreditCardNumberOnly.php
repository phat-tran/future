<?php

namespace App\Models;


/**
 * Class CreditCardNumberOnly
 *
 * @OA\Schema(
 *     description="Credit Card Number Only"
 * )
 */
class CreditCardNumberOnly
{
    const CARD_NUMBER = 'card_number';

    /**
     * Credit card number.
     *
     * @OA\Property(
     *     title="Number",
     *     description="Credit card number",
     *     type="integer",
     *     format="int64",
     *     example=374245455400126
     * )
     *
     * @var int
     */
    public $card_number;
}
