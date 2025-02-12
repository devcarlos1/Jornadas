<?php

namespace App\Http\Controllers;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Listar eventos con paginación
    public function eventList()
    {
        return response()->json(Event::with('speaker')->paginate(10));
    }
    
    public function viewEvent()
    {
        return view('admin.events');
    }

    // Registrar un nuevo evento con validación de horario
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'speaker_id' => 'required|exists:speakers,id',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'amount' => 'required',
            'max_attendees' => 'required|int',
        ]);

        // Verificar disponibilidad del horario
        $conflict = Event::where('speaker_id', $request->speaker_id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })
            ->exists();

        if ($conflict) {
            return response()->json(['message' => 'The speaker is not available at this time.'], 400);
        }

        // Crear evento
        $event = Event::create($request->all());

        return response()->json($event, 201);
    }

    // Mostrar un evento específico
    public function show($id)
    {
        $event = Event::with('speaker')->find($id);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }
        return response()->json($event);
    }

    // Eliminar un evento
    public function destroy($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $event->delete();
        return response()->json(['message' => 'Event deleted successfully']);
    }
}
