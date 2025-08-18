<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|numeric',
            'date' => 'required|date|after_or_equal:today',
            'phone' => 'required',
            'notes' => 'nullable|string',
            'status' => 'sometimes|string|in:pending,confirmed,cancelled',
            'time' => 'nullable|string',  // optional now
            'email' => 'nullable|email',  // optional now
        ]);

        $appointment = Appointment::create([
            'user_id'   => auth()->id(),
            'email'     => $request->email ?? auth()->user()->email ?? null,
            'phone'     => $request->phone,
            'service_id'=> $request->service_id,
            'date'      => $request->date,
            'time'      => $request->time ?? now()->format('H:i'),
            'notes'     => $request->notes ?? null,
            'status'    => $request->status ?? 'pending'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment created successfully',
            'appointment' => $appointment
        ], 201);
    }

    public function index()
    {
        $appointments = Appointment::with('service')
            ->where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($appointments);
    }

    public function history()
    {
        $appointments = Appointment::with('service')
            ->where('user_id', auth()->id())
            ->where('date', '<', now())
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($appointments);
    }

    public function show($id)
    {
        $appointment = Appointment::with('service')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json($appointment);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'phone' => 'required',
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'nullable|string',
            'status' => 'sometimes|string|in:pending,confirmed,cancelled',
        ]);

        $appointment->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment updated successfully',
            'appointment' => $appointment
        ]);
    }

    public function destroy($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $appointment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment deleted successfully'
        ]);
    }
}
