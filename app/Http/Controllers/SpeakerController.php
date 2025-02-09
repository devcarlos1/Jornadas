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
        ]);

        $speaker = Speaker::create($request->all());

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

