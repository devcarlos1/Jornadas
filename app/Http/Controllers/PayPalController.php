<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Auth;
use App\Models\Registration;
use App\Models\Payment;
use App\Models\Event;
use App\Mail\PaymentReceipt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PayPalController extends Controller
{
    public function createPayment(Request $request)
    {
        
        $paypal = new PayPalClient();
        $paypal = \PayPal::setProvider();
        $paypal->setApiCredentials( config('paypal'));
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
                "return_url" => route('paypal.success', [
                    'eventid' =>$request->eventid,
                    'type' => $request->type // Puedes agregar más datos si es necesario
                ])
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
                'users_id' => Auth::id(),
                'amount' => $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'],
                'status' => 'completed',
                'payment_method' => 'PayPal'
            ]);

            // Registrar la inscripción en la tabla registration con el payment_id
            Registration::create([
                'user_id' => Auth::id(),
                'event_id' => $request->eventid,
                'type' => $request->type,
                'payment_id' => $payment->id
            ]);
            $user = Auth::user();
            $ticketNumber = 'TICKET-' . strtoupper(Str::random(10));
            $event = Event::find($request->eventid);
            if($request->type === "Presencial"){
                $event->increment('total_attendees');
            }
            $event->increment('total_revenue', $response['purchase_units'][0]['payments']['captures'][0]['amount']['value']);
            Mail::to($user->email)->send(new PaymentReceipt($event, $user, $ticketNumber));

            return redirect()->route('users.eventUser')->with('success', 'Pago realizado con éxito y registro completado.');
        }

        return redirect()->route('users.eventList')->with('error', 'Error al procesar el pago.');
    }

    public function cancelPayment()
    {
        return redirect()->route('users.eventList')->with('error', 'Pago cancelado.');
    }
}
