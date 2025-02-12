<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\Event;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Mail\PaymentReceipt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function show()
    {
        return view('users.dashboard');
    }

    public function getUserEvents() {
        $user = Auth::user();
        
        // Obtener eventos a través de registros
        $events = Event::whereHas('registrations', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('speaker')->paginate(10);
    
        return response()->json([
            'events' => $events
        ]);

    }
    public function free(Request $request){

        $payment = Payment::create([
            'users_id' => Auth::id(),
            'amount' => 0,
            'status' => 'completed',
            'payment_method' => 'free'
        ]);
        // Registrar la inscripción en la tabla registration con el payment_id
        Registration::create([
            'user_id' => Auth::id(),
            'event_id' => $request->eventid,
            'type' => 'Gratuito',
            'payment_id' => $payment->id
        ]);
        $event = Event::find($request->eventid);
        $user = Auth::user();
        $ticketNumber = 'TICKET-' . strtoupper(Str::random(10));
        // Envía el correo con el comprobante
        Mail::to($user->email)->send(new PaymentReceipt($event, $user, $ticketNumber));
        $event->increment('total_attendees');
        $event->increment('total_revenue', 0);

        return redirect()->route('users.dashboard')->with('success', 'Pago realizado con éxito y registro completado.');
    }
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'event_id' => 'required|exists:events,id',
            'type' => 'required|string',
            'payment_id' => 'required',
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificar si el usuario ya está registrado en este evento
        if (Registration::where('user_id', $user->id)->where('event_id', $request->event_id)->exists()) {
            return back()->with('error', 'You are already registered for this event.');
        }

        // Registrar al usuario en el evento
        Registration::create([
            'user_id' => $user->id,
            'event_id' => $request->event_id,
            'type' => $request->type,
            'payment_id' => $request->payment_id,

        ]);

        return back()->with('success', 'You have successfully registered for the event.');
    }
}

