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
    public function showEventList()
    {
        return view('users.eventList');
    }
    public function showEventUser()
    {
        return view('users.eventUser');
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
            'photo' => 'required|mimes:jpeg,jpg,png',

        ]);
        $photo= $request->file('photo');

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

        if ($request->type == 'conferencia') {
            $conflict = Event::where('type', 'conferencia')
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                          ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
                })->exists();
    
            if ($conflict) {
                return response()->json(['error' => 'Ya hay una conferencia programada en ese horario.'], 422);
            }
        }
    
        // Verificar superposición de talleres en el aula específica
        if ($request->type == 'taller') {
            $conflict = Event::where('type', 'taller')
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                          ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
                })->exists();
    
            if ($conflict) {
                return response()->json(['error' => 'Ya hay un taller programado en ese horario.'], 422);
            }
        }
        // Crear evento
        $event = Event::create([
            'title' => $request->title,
            'type' => $request->type,
            'speaker_id' => $request->speaker_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'amount' => $request->amount,
            'max_attendees' => $request->max_attendees,
            'photo' => 'data:' . $photo->getMimeType() . ';base64,' . base64_encode(file_get_contents($photo->getRealPath()))
        ]);

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

    
    // Actualizar un evento
    public function update(Request $request, $id)
    {  
        
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'speaker_id' => 'required|exists:speakers,id',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'amount' => 'required',
            'max_attendees' => 'required|int',
            'photo' => 'required|mimes:jpeg,jpg,png',

        ]);
        $photo= $request->file('photo');
        $event = Event::findOrFail($id);
        // Crear evento
             $event->title= $request->title;
             $event->type = $request->type;
             $event->speaker_id = $request->speaker_id;
             $event->start_time = $request->start_time;
             $event->end_time = $request->end_time;
             $event->amount = $request->amount;
             $event->max_attendees = $request->max_attendees;
             $event->photo = 'data:' . $photo->getMimeType() . ';base64,' . base64_encode(file_get_contents($photo->getRealPath()));
             $event->save();

        return response()->json($event, 201);
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
