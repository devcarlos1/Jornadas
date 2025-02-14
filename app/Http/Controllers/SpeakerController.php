<?php

namespace App\Http\Controllers;

use App\Models\Speaker;
use Illuminate\Http\Request;

class SpeakerController extends Controller
{
    // Listar speakers con paginación
    public function spakersList()
    {
        return response()->json(Speaker::paginate(10));
    }
    public function viewSpeaker()
    {
        return view('admin.speakers');
    }

    // Registrar un nuevo speaker
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'expertise' => 'required|string|max:255',
            'social_links' => 'required|string|max:255',
            'photo' => 'required|mimes:jpeg,jpg,png',
        ]);
        $photo= $request->file('photo');
        $speaker = Speaker::create([
            'name' => $request->name,
            'expertise' => $request->expertise,
            'social_links' => $request->social_links,
            'photo' => 'data:' . $photo->getMimeType() . ';base64,' . base64_encode(file_get_contents($photo->getRealPath()))
        ]);

        return response()->json($speaker, 201);
    }

    // Mostrar un speaker específico
    public function show($id)
    {
        $speaker = Speaker::find($id);
        if (!$speaker) {
            return response()->json(['message' => 'Speaker not found'], 404);
        }
        return response()->json($speaker);
    }
    // Actualizar un speaker
    public function update(Request $request, $id)
    {  
        
        $request->validate([
            'name' => 'required|string|max:255',
            'expertise' => 'required|string|max:255',
            'social_links' => 'required|string|max:255',
            'photo' => 'required|mimes:jpeg,jpg,png',
        ]);
        $photo= $request->file('photo');
        $speaker = Speaker::findOrFail($id);
        // Crear evento
             $speaker->name= $request->name;
             $speaker->expertise = $request->expertise;
             $speaker->social_links = $request->social_links;
             $speaker->photo = 'data:' . $photo->getMimeType() . ';base64,' . base64_encode(file_get_contents($photo->getRealPath()));
             $speaker->save();

        return response()->json($speaker, 201);
    }
    // Eliminar un speaker
    public function destroy($id)
    {
        $speaker = Speaker::find($id);
        if (!$speaker) {
            return response()->json(['message' => 'Speaker not found'], 404);
        }

        $speaker->delete();
        return response()->json(['message' => 'Speaker deleted successfully']);
    }
}

