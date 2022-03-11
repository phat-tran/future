<?php

namespace App\Models;


/**
 * Class CreditCard
 *
 * @OA\Schema(
 *     description="Credit Card"
 * )
 */
class CreditCard extends CreditCardNumberOnly
{
    const CARD_NAME   = 'card_name';
    const VALID_UNTIL = 'valid_until';
    const CCV         = 'ccv';

    /**
     * Credit card name.
     *
     * @OA\Property(
     *     title="Name",
     *     description="Credit card name",
     *     example="John Doe"
     * )
     *
     * @var string
     */
    public $card_name;

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

    /**
     * Credit card valid until.
     *
     * @OA\Property(
     *     title="Valid Until",
     *     description="Credit card valid until",
     *     type="string",
     *     format="date",
     *     example="2022-02-22"
     * )
     *
     * @var string
     */
    public $valid_until;

    /**
     * Credit card ccv number.
     *
     * @OA\Property(
     *     title="CCV number",
     *     description="Credit card CCV number",
     *     type="integer",
     *     format="int32",
     *     example=123
     * )
     *
     * @var int
     */
    public $ccv;
}
