<?php

namespace App\Models;


use App\Constants\Constant;
use DOMDocument;
use Faker\Factory;
use ReflectionClass;


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

    /**
     * Creates instance of this class.
     *
     * @param CreditCard $card   The credit card.
     * @param float      $amount The amount to pay.
     *
     * @return static
     */
    public static function createInstance(CreditCard $card, float $amount): Payment
    {
        $faker                  = Factory::create();
        $instance               = new static();
        $instance->from         = $card;
        $instance->merchant_id  = $faker->numberBetween(1, 10000);
        $instance->merchant_key = $faker->regexify('[a-z0-9]{10}');
        $instance->amount       = $amount;

        return $instance;
    }

    /**
     * Creates a payment xml.
     *
     * @param Payment $payment The payment object.
     *
     * @return string
     */
    public static function createPaymentXml(Payment $payment): string
    {
        $xml            = new DOMDocument(Constant::XML_VERSION, Constant::XML_ENCODING);
        $root           = $xml->createElement((new ReflectionClass(Payment::class))->getShortName());
        $from           = $xml->createElement(Payment::FROM);
        $cardNumber     = $xml->createElement(CreditCard::CARD_NUMBER, $payment->from->card_number);
        $cardName       = $xml->createElement(CreditCard::CARD_NAME, $payment->from->card_name);
        $cardCvv        = $xml->createElement(CreditCard::CCV, $payment->from->ccv);
        $cardValidUntil = $xml->createElement(CreditCard::VALID_UNTIL, $payment->from->valid_until);
        $from->appendChild($cardNumber);
        $from->appendChild($cardName);
        $from->appendChild($cardCvv);
        $from->appendChild($cardValidUntil);
        $root->appendChild($from);
        $amount      = $xml->createElement(Payment::AMOUNT, $payment->amount);
        $merchantId  = $xml->createElement(Payment::MERCHANT_ID, $payment->merchant_id);
        $merchantKey = $xml->createElement(Payment::MERCHANT_KEY, $payment->merchant_key);
        $root->appendChild($amount);
        $root->appendChild($merchantId);
        $root->appendChild($merchantKey);
        $xml->appendChild($root);

        return $xml->saveXML();
    }

    /**
     * Creates a payment Json.
     *
     * @param Payment $payment The payment object.
     *
     * @return array
     */
    public static function createPaymentJson(Payment $payment): array
    {
        return json_decode(json_encode($payment), true);
    }
}
