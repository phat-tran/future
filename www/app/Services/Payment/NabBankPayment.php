<?php

namespace App\Services\Payment;


use App\Constants\Constant;
use App\Models\CreditCard;
use App\Models\Payment;

/**
 * Payment by NAB.
 */
class NabBankPayment extends BankPayment
{
    /**
     * Sets payment request body.
     */
    protected function setBody(): void
    {
        $requestPaymentXml = Payment::createPaymentXml($this->payment);

        $this->body = [
            'body' => $requestPaymentXml,
        ];
    }

    /**
     * Sets payment endpoint.
     */
    protected function setEndpoint(): void
    {
        $this->endpoint = config('app.api_host') . Constant::API_PAYMENT_NAB_URL;
    }

    /**
     * Sets request headers.
     */
    protected function setHeaders(): void
    {
        $this->headers = [
            'Content-Type'  => 'text/xml;charset=utf-8',
            'Authorization' => 'Bearer ' . $this->token,
        ];
    }
}
