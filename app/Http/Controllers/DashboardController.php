<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Person;
use App\Models\Fee;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRooms = Room::count();
        $totalPersons = Person::where('is_active', true)->count();
        $totalCollected = Fee::where('status', 'paid')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
        $totalPending = Fee::where('status', 'pending')
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $recentFees = Fee::with('person.room')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $rooms = Room::withCount(['persons' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        return view('dashboard', compact(
            'totalRooms', 'totalPersons', 'totalCollected',
            'totalPending', 'recentFees', 'rooms'
        ));
    }
}
