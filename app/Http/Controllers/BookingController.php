<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'booking_date' => 'required|date',
        ]);

        $event = Event::findOrFail($request->event_id);

        $bookingDate = Carbon\Carbon::parse($request->booking_date);
        $eventStart = Carbon::parse($event->start_time)->startOfDay();
        $eventEnd = Carbon::parse($event->end_time)->endOfDay();

        // Validate booking date within event range
        if (!$bookingDate->between($eventStart, $eventEnd)) {
            return response()->json([
                'message' => 'Booking date must fall between event start and end date.'
            ], 422);
        }

        $bookingCount = Booking::where('event_id', $event->id)->count();

        //Validate booking capacity for overbooking the event.
        if ($bookingCount >= $event->capacity) {
            return response()->json(['message' => 'Event is fully booked.'], 400);
        }

        //Validate same user is not booking same event multiple times.
        $exists = Booking::where('event_id', $event->id)
                    ->where('attendee_id', Auth::id())
                    ->exists();

        if ($exists) {
            return response()->json(['message' => 'Attendee already booked for this event.'], 400);
        }

        $booking = Booking::create([
            'event_id' => $event->id,
            'attendee_id' => Auth::id(),
            'booking_date' => $bookingDate->toDateString()
        ]);

        return response()->json($booking, 201);
    }
}
