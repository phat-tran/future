<?php

namespace App\Services\Payment;


use App\Constants\Constant;
use App\Models\CreditCard;
use App\Models\CreditCardNumberOnly;
use App\Models\Payment;
use App\Models\PaymentResponse;

/**
 * Payment by ANZ.
 */
class AnzBankPayment extends BankPayment
{
    /**
     * Sets payment request body.
     */
    public function setBody(): void
    {
        $requestPaymentJson = Payment::createPaymentJson($this->payment);

        $this->body = [
            'body' => $requestPaymentJson,
        ];
    }

    /**
     * Sets payment endpoint.
     */
    protected function setEndpoint(): void
    {
        $this->endpoint = config('app.api_host') . Constant::API_PAYMENT_ANZ_URL;
    }

    /**
     * Sets request headers.
     */
    protected function setHeaders(): void
    {
        $this->headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ];
    }
}
