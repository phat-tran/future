<?php

namespace App\Http\Controllers;


use App\Models\CreditCard;
use App\Models\Payment;
use App\Services\Payment\AnzBankPayment;
use App\Services\Payment\NabBankPayment;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Class PaymentController manages payments.
 */
class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Payment by NAB.
     *
     * @param Request $request
     *
     * @return Response|JsonResponse
     * @throws Exception
     */
    public function paymentNab(Request $request)
    {
        try
        {
            $request->validate([
                CreditCard::CARD_NAME   => 'string|required',
                CreditCard::CARD_NUMBER => 'int|required',
                CreditCard::CCV         => 'int|required|digits:3',
                CreditCard::VALID_UNTIL => 'date|required',
                Payment::AMOUNT         => 'numeric|required',
            ]);

            $card_name         = $request->get(CreditCard::CARD_NAME);
            $card_number       = (int)$request->get(CreditCard::CARD_NUMBER);
            $cvv               = (int)$request->get(CreditCard::CCV);
            $valid_until       = $request->get(CreditCard::VALID_UNTIL);
            $amount            = (float)$request->get(Payment::AMOUNT);
            $card              = new CreditCard();
            $card->card_name   = $card_name;
            $card->card_number = $card_number;
            $card->ccv         = $cvv;
            $card->valid_until = $valid_until;
            $paymentService    = new PaymentService(new NabBankPayment($card, $amount));

            return $paymentService->pay();
        }
        catch (Exception $e)
        {
            Log::error($e);

            return response()->json($e->getMessage(), HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Payment by ANZ.
     *
     * @param Request $request
     *
     * @return Response|JsonResponse
     * @throws Exception
     */
    public function paymentAnz(Request $request)
    {
        try
        {
            $request->validate([
                CreditCard::CARD_NAME   => 'string|required',
                CreditCard::CARD_NUMBER => 'int|required',
                CreditCard::CCV         => 'int|required|digits:3',
                CreditCard::VALID_UNTIL => 'date|required',
                Payment::AMOUNT         => 'numeric|required',
            ]);

            $card_name         = $request->get(CreditCard::CARD_NAME);
            $card_number       = (int)$request->get(CreditCard::CARD_NUMBER);
            $cvv               = (int)$request->get(CreditCard::CCV);
            $valid_until       = $request->get(CreditCard::VALID_UNTIL);
            $amount            = (float)$request->get(Payment::AMOUNT);
            $card              = new CreditCard();
            $card->card_name   = $card_name;
            $card->card_number = $card_number;
            $card->ccv         = $cvv;
            $card->valid_until = $valid_until;
            $paymentService    = new PaymentService(new AnzBankPayment($card, $amount));

            return $paymentService->pay();
        }
        catch (Exception $e)
        {
            Log::error($e);

            return response()->json($e->getMessage(), HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
