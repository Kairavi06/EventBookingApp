<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query();

        // Optional filters
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('date')) {
            $query->whereDate('start_time', '>=', $request->date);
        }

        // Paginate with default 10 per page
        $events = $query->paginate($request->get('per_page', 10));

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'capacity' => 'required|integer|min:1',
            'country' => 'required|string|max:100',
        ]);

        $event = Event::create($validated);

        return response->json($event, 200);
    }

    public function show(Event $event)
    {
        return $event;
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
            'capacity' => 'sometimes|required|integer|min:1',
            'country' => 'required|string|max:100',
        ]);

        $event->update($validated);

        return $event;
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return response()->noContent();
    }
}
