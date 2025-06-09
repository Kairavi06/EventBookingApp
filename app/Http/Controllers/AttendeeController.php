<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AttendeeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:attendees,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        return Attendee::create($validated);
    }

    public function show()
    {
        return response()->json(Auth::user());
    }

    public function update(Request $request)
    {
        $attendee = Auth::user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:attendees,email,' . $attendee->id,
        ]);

        $attendee->update($validated);

        return response()->json($attendee);
    }

    public function destroy()
    {
        $attendee = Auth::user();
        $attendee->delete();

        return response()->noContent();
    }
}