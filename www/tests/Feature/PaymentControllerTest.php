<?php

namespace Tests\Feature;


use App\Constants\Constant;
use App\Models\CreditCard;
use App\Models\CreditCardNumberOnly;
use App\Models\CreditCardPayment;
use App\Models\Payment;
use App\Models\PaymentResponse;
use App\Models\User;
use DateTime;
use DOMDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use ReflectionClass;
use Tests\TestCase;
use Faker\Factory;

class PaymentControllerTest extends TestCase
{
    /**
     * Tests payment using NAB.
     *
     * @return void
     */
    public function testPaymentNab()
    {
        $url = Constant::PAYMENT_NAB_POST_URL;

        // Mocks some fake objects to make API request.

        $creditCard         = self::fakeCreditCard();
        $requestPayment     = self::fakeCreditCardPayment($creditCard);
        $requestPaymentJson = self::fakeRequestPaymentJson($requestPayment);

        // Set headers.

        $headers = [
            'Content-Type' => 'application/json',
        ];

        // Now post the request to receive the fake response.

        $response = $this->postJson($url, $requestPaymentJson, $headers);
        $response->assertStatus(401);
        $user     = User::factory()->create();
        $response = $this
            ->actingAs($user)
            ->postJson($url, $requestPaymentJson, $headers);

        // Assertions. Test the card number and the amount we requested are the same with the returned response.

        $response->assertStatus(200);

        /** @var PaymentResponse $xml */
        $xml = simplexml_load_string($response->getContent());
        $this->assertEquals($requestPayment->card_number, $xml->from->card_number);
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
    public function testPaymentAnz()
    {
        $url = Constant::PAYMENT_ANZ_POST_URL;

        // Mocks some fake objects to make API request.

        $creditCard         = self::fakeCreditCard();
        $requestPayment     = self::fakeCreditCardPayment($creditCard);
        $requestPaymentJson = self::fakeRequestPaymentJson($requestPayment);

        // Set headers.

        $headers = [
            'Content-Type' => 'application/json',
        ];

        // Now post the request to receive the fake response.

        $response = $this->postJson($url, $requestPaymentJson, $headers);
        $response->assertStatus(401);
        $user     = User::factory()->create();
        $response = $this
            ->actingAs($user)
            ->postJson($url, $requestPaymentJson, $headers);

        // Assertions. Test the card number and the amount we requested are the same with the returned response.

        $response->assertStatus(200);

        /** @var PaymentResponse $json */
        $json = json_decode($response->getContent(), false);
        $this->assertEquals($requestPayment->card_number, $json->from->card_number);
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
     * @param CreditCard $card The credit card object.
     *
     * @return CreditCardPayment
     */
    private static function fakeCreditCardPayment(CreditCard $card): CreditCardPayment
    {
        $faker                = Factory::create();
        $payment              = new CreditCardPayment();
        $payment->card_number = $card->card_number;
        $payment->card_name   = $card->card_name;
        $payment->ccv         = $card->ccv;
        $payment->valid_until = $card->valid_until;
        $payment->amount      = $faker->randomFloat(2, 0, 1000);

        return $payment;
    }

    /**
     * Creates a fake request payment JSON.
     *
     * @param CreditCardPayment $payment
     *
     * @return array
     */
    private static function fakeRequestPaymentJson(CreditCardPayment $payment): array
    {
        return json_decode(json_encode($payment), true);
    }
}
