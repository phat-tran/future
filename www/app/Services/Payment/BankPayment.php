<?php

namespace App\Services\Payment;


use App\Constants\Constant;
use App\Models\CreditCard;
use App\Models\Payment;
use App\Models\PaymentResponse;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Payment abstraction.
 */
abstract class BankPayment
{
    /**
     * The request payment object.
     *
     * @var Payment
     */
    protected $payment;

    /**
     * The response payment.
     *
     * @var Response
     */
    protected $paymentResponse;

    /**
     * The access token string.
     *
     * @var string
     */
    protected $token;

    /**
     * The endpoint to make payment.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * The request body.
     *
     * @var string|array
     */
    protected $body;

    /**
     * The request headers.
     *
     * @var array<string, string>
     */
    protected $headers;


    /**
     * Constructor.
     *
     * @param CreditCard $card
     * @param float      $amount
     *
     * @throws Exception
     */
    public function __construct(CreditCard $card, float $amount)
    {
        $this->payment = Payment::createInstance($card, $amount);
        $this->setAccessToken();
        $this->setEndpoint();
        $this->setHeaders();
        $this->setBody();
    }

    /**
     * Pays an invoice.
     *
     * @return Response
     */
    public function pay(): Response
    {
        return Http
            ::withHeaders($this->headers)
            ->post($this->endpoint, $this->body);
    }

    /**
     * Sets access token.
     *
     * @throws Exception
     */
    private function setAccessToken(): void
    {
        $response = Http::post(config('app.api_host') . Constant::API_OAUTH_TOKEN_URL, [
            'client_secret' => config('app.api_client_secret'),
            'client_id'     => config('app.api_client_id'),
            'grant_type'    => config('app.api_grant_type'),
            'username'      => config('app.api_username'),
            'password'      => config('app.api_password'),
        ]);

        if (!isset($response))
        {
            throw new Exception('Unable to get access token');
        }

        $json = $response->json();

        if (!isset($json, $json['access_token']))
        {
            throw new Exception('Unable to get access token');
        }

        $this->token = $json['access_token'];
    }

    /**
     * Sets payment request body.
     */
    abstract protected function setBody(): void;

    /**
     * Sets payment endpoint.
     */
    abstract protected function setEndpoint(): void;

    /**
     * Sets request headers.
     */
    abstract protected function setHeaders(): void;
}
