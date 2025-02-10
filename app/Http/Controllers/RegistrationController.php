<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    public function viewDash()
    {
        return view('users.dashboard');
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

