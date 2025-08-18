<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function login(Request \$request)
    {
        \$request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt(\$request->only('email', 'password'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid login credentials'
            ], 401);
        }

        \$user = Auth::user();
        if (\$user->role !== 'admin') {
            Auth::logout();
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'user' => \$user
        ]);
    }

    public function appointments()
    {
        \$appointments = Appointment::with(['user', 'service'])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json(\$appointments);
    }

    public function users()
    {
        \$users = User::where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(\$users);
    }

    public function updateAppointment(Request \$request, \$id)
    {
        \$appointment = Appointment::findOrFail(\$id);

        \$request->validate([
            'status' => 'required|string|in:pending,confirmed,cancelled'
        ]);

        \$appointment->update(\$request->only('status'));

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment updated successfully',
            'appointment' => \$appointment
        ]);
    }

    public function destroyAppointment(\$id)
    {
        \$appointment = Appointment::findOrFail(\$id);
        \$appointment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment deleted successfully'
        ]);
    }
}
