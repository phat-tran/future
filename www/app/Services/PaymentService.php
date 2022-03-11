<?php

namespace App\Services;


use App\Services\Payment\BankPayment;
use Illuminate\Http\Client\Response;

/**
 * Class PaymentService handles all payment operations.
 */
class PaymentService
{
    /**
     * The payment object.
     *
     * @var BankPayment
     */
    protected $payment;

    /**
     * Constructor.
     *
     * @param BankPayment $payment
     */
    public function __construct(BankPayment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Pays an invoice.
     *
     * @return Response
     */
    public function pay(): string
    {
        return $this->payment->pay();
    }
}
