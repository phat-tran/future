<?php

namespace Tests\Feature;


use App\Constants\Constant;
use App\Models\CreditCard;
use App\Models\CreditCardNumberOnly;
use App\Models\Payment;
use App\Models\PaymentResponse;
use Carbon\Carbon;
use DateTime;
use DOMDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use ReflectionClass;
use Tests\TestCase;
use Faker\Factory;

class PaymentTest extends TestCase
{
    const API_PAYMENT_NAB_URL = AuthTest::API_URL . '/payment/nab';
    const API_PAYMENT_ANZ_URL = AuthTest::API_URL . '/payment/anz';

    /**
     * The bearer token key.
     *
     * @var string
     */
    protected $token;

    /**
     * Initial set up.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $response = Http::post(AuthTest::API_OAUTH_TOKEN_URL, [
            'client_secret' => config('app.client_secret'),
            'client_id'     => config('app.client_id'),
            'grant_type'    => config('app.grant_type'),
            'username'      => config('app.username'),
            'password'      => config('app.password'),
        ]);

        $this->token = $response->json()['access_token'];
    }

    /**
     * Tests payment using NAB.
     *
     * @return void
     */
    public function testPaymentNab()
    {
        $this->assertNotNull($this->token);
        $url = self::API_PAYMENT_NAB_URL;

        // Mocks some fake objects to make API request.

        $creditCard           = self::fakeCreditCard();
        $requestPayment       = self::fakeRequestPayment($creditCard);
        $requestPaymentXml    = self::fakeRequestPaymentXml($requestPayment);
        $creditCardNumberOnly = self::fakeCreditCardNumberOnly($creditCard);
        $responsePayment      = self::fakeResponsePayment($creditCardNumberOnly, $requestPayment->amount);
        $responsePaymentXml   = self::fakeResponsePaymentXml($responsePayment);

        // Set headers.

        $headers = [
            'Content-Type'  => 'text/xml;charset=utf-8',
            'Authorization' => 'Bearer ' . $this->token,
        ];

        // Fake the response for the purpose of testing the testing code.
        // TODO: Remove the fake Http response once the API is up.

        Http::fake([
            $url => Http::response($responsePaymentXml, 200, $headers),
        ]);

        // Now post the request to receive the fake response.

        $body = [
            'body' => $requestPaymentXml,
        ];

        $response = Http
            ::withHeaders($headers)
            ->post($url, $body);

        // Assertions. Test the card number and the amount we requested are the same with the returned response.

        $this->assertTrue($response->status() === 200);
        $this->assertTrue($response->header('Content-Type') === 'text/xml;charset=utf-8');
        $this->assertXmlStringEqualsXmlString($responsePaymentXml, $response->body());

        /** @var PaymentResponse $xml */
        $xml = simplexml_load_string($response->body());
        $this->assertEquals($requestPayment->from->card_number, $xml->from->card_number);
        $this->assertEquals($requestPayment->amount, (float)$xml->amount);
        $this->assertNotNull($xml->transaction_number);
        $date = DateTime::createFromFormat(Constant::DATE_TIME_FORMAT, $xml->transaction_time);
        $this->assertTrue($date && $date->format(Constant::DATE_TIME_FORMAT) ===
            (string)$xml->transaction_time);
    }

    /**
     * Tests payment using ANZ.
     *
     * @return void
     */
    public function testPaymentANZ()
    {
        $url = self::API_PAYMENT_ANZ_URL;

        // Mocks some fake objects to make API request.

        $creditCard           = self::fakeCreditCard();
        $requestPayment       = self::fakeRequestPayment($creditCard);
        $requestPaymentJson   = self::fakeRequestPaymentJson($requestPayment);
        $creditCardNumberOnly = self::fakeCreditCardNumberOnly($creditCard);
        $responsePayment      = self::fakeResponsePayment($creditCardNumberOnly, $requestPayment->amount);
        $responsePaymentJson  = self::fakeResponsePaymentJson($responsePayment);

        // Set headers.

        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ];

        // Fake the response for the purpose of testing the testing code.
        // TODO: Remove the fake Http response once the API is up.

        Http::fake([
            $url => Http::response($responsePaymentJson, 200, $headers),
        ]);

        // Now post the request to receive the fake response.

        $body = [
            'body' => $requestPaymentJson,
        ];

        $response = Http
            ::withHeaders($headers)
            ->post($url, $body);

        // Assertions. Test the card number and the amount we requested are the same with the returned response.

        $this->assertTrue($response->status() === 200);
        $this->assertTrue($response->header('Content-Type') === 'application/json');
        $this->assertEqualsCanonicalizing($responsePaymentJson, json_decode($response->body(), true));

        /** @var PaymentResponse $json */
        $json = json_decode($response->body(), false);
        $this->assertEquals($requestPayment->from->card_number, $json->from->card_number);
        $this->assertEquals($requestPayment->amount, $json->amount);
        $this->assertNotNull($json->transaction_number);
        $date = DateTime::createFromFormat(Constant::DATE_TIME_FORMAT, $json->transaction_time);
        $this->assertTrue($date && $date->format(Constant::DATE_TIME_FORMAT) === $json->transaction_time);
    }

    /**
     * Fakes credit card details.
     *
     * @return CreditCard
     */
    private static function fakeCreditCard(): CreditCard
    {
        $faker             = Factory::create();
        $card              = new CreditCard();
        $card->card_name   = $faker->name;
        $card->card_number = $faker->creditCardNumber;
        $card->ccv         = $faker->randomNumber(3);
        $card->valid_until = $faker->creditCardExpirationDate->format(Constant::DATE_FORMAT);

        return $card;
    }

    /**
     * Fakes credit card number only details.
     *
     * @param CreditCard $originalCard
     *
     * @return CreditCardNumberOnly
     */
    private static function fakeCreditCardNumberOnly(CreditCard $originalCard): CreditCardNumberOnly
    {
        $card              = new CreditCardNumberOnly();
        $card->card_number = $originalCard->card_number;

        return $card;
    }

    /**
     * Fakes request payment details.
     *
     * @param CreditCard $card
     *
     * @return Payment
     */
    private static function fakeRequestPayment(CreditCard $card): Payment
    {
        $faker                 = Factory::create();
        $payment               = new Payment();
        $payment->from         = $card;
        $payment->amount       = $faker->randomFloat(2, 0, 1000);
        $payment->merchant_id  = $faker->numberBetween(1, 10000);
        $payment->merchant_key = $faker->regexify('[a-z0-9]{10}');

        return $payment;
    }

    /**
     * Fakes response payment details.
     *
     * @param CreditCardNumberOnly $card
     * @param float                $amount
     *
     * @return PaymentResponse
     */
    private static function fakeResponsePayment(CreditCardNumberOnly $card, float $amount): PaymentResponse
    {
        $faker                       = Factory::create();
        $payment                     = new PaymentResponse();
        $payment->from               = $card;
        $payment->amount             = $amount;
        $payment->transaction_number = $faker->numberBetween(1, 10000);
        $payment->transaction_time   = now()->format(Constant::DATE_TIME_FORMAT);

        return $payment;
    }

    /**
     * Creates a fake request payment xml.
     *
     * @param Payment $payment
     *
     * @return string
     */
    private static function fakeRequestPaymentXml(Payment $payment): string
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
     * Creates a fake response payment xml.
     *
     * @param PaymentResponse $payment
     *
     * @return string
     */
    private static function fakeResponsePaymentXml(PaymentResponse $payment): string
    {
        $xml        = new DOMDocument(Constant::XML_VERSION, Constant::XML_ENCODING);
        $root       = $xml->createElement((new ReflectionClass(PaymentResponse::class))->getShortName());
        $from       = $xml->createElement(PaymentResponse::FROM);
        $cardNumber = $xml->createElement(CreditCard::CARD_NUMBER, $payment->from->card_number);
        $from->appendChild($cardNumber);
        $root->appendChild($from);
        $amount      = $xml->createElement(PaymentResponse::AMOUNT, $payment->amount);
        $merchantId  = $xml->createElement(PaymentResponse::TRANSACTION_NUMBER, $payment->transaction_number);
        $merchantKey = $xml->createElement(PaymentResponse::TRANSACTION_TIME, $payment->transaction_time);
        $root->appendChild($amount);
        $root->appendChild($merchantId);
        $root->appendChild($merchantKey);
        $xml->appendChild($root);

        return $xml->saveXML();
    }

    /**
     * Creates a fake request payment JSON.
     *
     * @param Payment $payment
     *
     * @return array
     */
    private static function fakeRequestPaymentJson(Payment $payment): array
    {
        return json_decode(json_encode($payment), true);
    }

    /**
     * Creates a fake response payment JSON.
     *
     * @param PaymentResponse $payment
     *
     * @return array
     */
    private static function fakeResponsePaymentJson(PaymentResponse $payment): array
    {
        return json_decode(json_encode($payment), true);
    }
}
