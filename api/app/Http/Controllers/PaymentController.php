<?php

namespace App\Http\Controllers;


use App\Models\Payment;
use App\Models\PaymentResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @OA\Info(
 *    title="A Future Corporation API",
 *    version="1.0.0",
 * )
 */
class PaymentController extends Controller
{
    /**
     * @OA\Post(
     *      path="/payment/nab",
     *      summary="Pay with NAB",
     *      description="Pay credit card with NAB",
     *      operationId="payNab",
     *      tags={"Payment"},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Credit card details",
     *          @OA\XmlContent(ref="#/components/schemas/Payment"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Payment succeeded.",
     *          @OA\XmlContent(ref="#/components/schemas/PaymentResponse")
     *     ),
     *      @OA\Response(
     *          response=406,
     *          description="Payment failed response",
     *          @OA\XmlContent(ref="#/components/schemas/PaymentResponse")
     *      ),
     * )
     *
     * @param Request $request
     *
     * @return string
     */
    public function paymentNab(Request $request)
    {
        $payment = json_decode($request->getContent());
        /** @var Payment $paymentObj */
        $paymentObj            = simplexml_load_string($payment->body);
        $payment               = new Payment();
        $payment->from         = $paymentObj->from;
        $payment->amount       = $paymentObj->amount;
        $payment->merchant_id  = $paymentObj->merchant_id;
        $payment->merchant_key = $paymentObj->merchant_key;

        return PaymentResponse::createPaymentResponseXml(PaymentResponse::createInstance($payment));
    }

    /**
     * @OA\Post(
     *      path="/payment/anz",
     *      summary="Pay with ANZ",
     *      description="Pay credit card with ANZ",
     *      operationId="payAnz",
     *      tags={"Payment"},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Credit card details",
     *          @OA\JsonContent(ref="#/components/schemas/Payment"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Payment succeeded.",
     *          @OA\JsonContent(ref="#/components/schemas/PaymentResponse")
     *     ),
     *      @OA\Response(
     *          response=406,
     *          description="Payment failed response",
     *          @OA\JsonContent(ref="#/components/schemas/PaymentResponse")
     *      ),
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function paymentAnz(Request $request)
    {
        $payment = json_decode($request->getContent());
        /** @var Payment $paymentObj */
        $paymentObj            = $payment->body;
        $payment               = new Payment();
        $payment->from         = $paymentObj->from;
        $payment->amount       = $paymentObj->amount;
        $payment->merchant_id  = $paymentObj->merchant_id;
        $payment->merchant_key = $paymentObj->merchant_key;

        return response()->json(PaymentResponse::createPaymentResponseJson(PaymentResponse::createInstance($payment)));
    }
}
