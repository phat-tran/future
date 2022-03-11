<?php

namespace App\Models;


use App\Constants\Constant;
use DOMDocument;
use Faker\Factory;
use ReflectionClass;

/**
 * Class PaymentResponse
 *
 * @OA\Schema(
 *     description="Payment Response"
 * )
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
     * @OA\Property(
     *     ref="#/components/schemas/CreditCardNumberOnly"
     * )
     *
     * @var CreditCardNumberOnly
     */
    public $from;

    /**
     * Transaction number.
     *
     * @OA\Property(
     *     title="Transaction number",
     *     description="Number of transaction",
     *     type="integer",
     *     format="int32",
     *     example=123
     * )
     *
     * @var int
     */
    public $transaction_number;

    /**
     * Transaction time.
     *
     * @OA\Property(
     *     title="Transaction time",
     *     description="Time of transaction",
     *     format="date-time",
     *     example="2022-02-02 02:02:02"
     * )
     *
     * @var string
     */
    public $transaction_time;

    /**
     * Amount paid.
     *
     * @OA\Property(
     *     title="Amount paid",
     *     description="Amount paid",
     *     type="number",
     *     format="float",
     *     example=14.2
     * )
     *
     * @var float
     */
    public $amount;

    /**
     * Creates instance of this class.
     *
     * @param Payment $payment
     *
     * @return static
     */
    public static function createInstance(Payment $payment): PaymentResponse
    {
        $faker                        = Factory::create();
        $instance                     = new static();
        $instance->from               = new CreditCardNumberOnly();
        $instance->from->card_number  = $payment->from->card_number;
        $instance->transaction_number = $faker->numberBetween(1, 10000);
        $instance->transaction_time   = now()->format(Constant::DATE_TIME_FORMAT);
        $instance->amount             = $payment->amount;

        return $instance;
    }

    /**
     * Creates a payment xml.
     *
     * @param PaymentResponse $payment The payment response object.
     *
     * @return string
     */
    public static function createPaymentResponseXml(PaymentResponse $payment): string
    {
        $xml        = new DOMDocument(Constant::XML_VERSION, Constant::XML_ENCODING);
        $root       = $xml->createElement((new ReflectionClass(PaymentResponse::class))->getShortName());
        $from       = $xml->createElement(PaymentResponse::FROM);
        $cardNumber = $xml->createElement(CreditCard::CARD_NUMBER, $payment->from->card_number);
        $from->appendChild($cardNumber);
        $root->appendChild($from);
        $amount            = $xml->createElement(PaymentResponse::AMOUNT, $payment->amount);
        $transactionNumber = $xml->createElement(PaymentResponse::TRANSACTION_NUMBER,
            $payment->transaction_number);
        $transactionTime   = $xml->createElement(PaymentResponse::TRANSACTION_TIME,
            $payment->transaction_time);
        $root->appendChild($amount);
        $root->appendChild($transactionNumber);
        $root->appendChild($transactionTime);
        $xml->appendChild($root);

        return $xml->saveXML();
    }

    /**
     * Creates a payment response Json.
     *
     * @param PaymentResponse $payment The payment response object.
     *
     * @return array
     */
    public static function createPaymentResponseJson(PaymentResponse $payment): array
    {
        return json_decode(json_encode($payment), true);
    }
}
