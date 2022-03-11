<?php

namespace App\Models;


/**
 * Class Payment
 *
 * @OA\Schema(
 *     description="Payment"
 * )
 */
class Payment
{
    /**
     * Merchant id.
     *
     * @OA\Property(
     *     ref="#/components/schemas/CreditCard"
     * )
     *
     * @var int
     */
    public $from;

    /**
     * Merchant id.
     *
     * @OA\Property(
     *     title="Merchant Id",
     *     description="ID of merchant",
     *     type="integer",
     *     format="int32",
     *     example=123
     * )
     *
     * @var int
     */
    public $merchant_id;

    /**
     * Merchant key.
     *
     * @OA\Property(
     *     title="Merchant Key",
     *     description="Key of merchant",
     *     example="John Doe"
     * )
     *
     * @var string
     */
    public $merchant_key;

    /**
     * Amount to be paid.
     *
     * @OA\Property(
     *     title="Amount to be paid",
     *     description="Amount to be paid",
     *     type="number",
     *     format="float",
     *     example=14.2
     * )
     *
     * @var float
     */
    public $amount;
}
