<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Auth;
use App\Models\Registration;


class PayPalController extends Controller
{
    public function createPayment(Request $request)
    {
        $paypal = new PayPalClient();
        $paypal->setApiCredentials(config('paypal'));
        $paypal->getAccessToken();

        $response = $paypal->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => "USD",
                    "value" => $request->amount // Monto del pago
                ]
            ]],
            "application_context" => [
                "cancel_url" => route('paypal.cancel'),
                "return_url" => route('paypal.success')
            ]
        ]);

        if (isset($response['id']) && $response['status'] == "CREATED") {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        }

        return back()->with('error', 'Error al procesar el pago con PayPal.');
    }

    public function successPayment(Request $request)
    {
        $paypal = new PayPalClient();
        $paypal->setApiCredentials(config('paypal'));
        $paypal->getAccessToken();
        $response = $paypal->capturePaymentOrder($request->token);

        if (isset($response['status']) && $response['status'] == "COMPLETED") {
            // Registrar el pago exitoso
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'status' => 'completed',
                'payment_method' => 'PayPal'
            ]);

            // Registrar la inscripción en la tabla registration con el payment_id
            Registration::create([
                'user_id' => Auth::id(),
                'event_id' => $request->event_id,
                'type' => $request->type,
                'payment_id' => $payment->id
            ]);

            return redirect()->route('users/dashboard')->with('success', 'Pago realizado con éxito y registro completado.');
        }

        return redirect()->route('users/dashboard')->with('error', 'Error al procesar el pago.');
    }

    public function cancelPayment()
    {
        return redirect()->route('users/dashboard')->with('error', 'Pago cancelado.');
    }
}
