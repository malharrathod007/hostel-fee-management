<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Room;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $query = Person::with('room');

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $persons = $query->orderBy('name')->get();

        $totalDeposit = $persons->sum('deposit');
        $paidCount    = $persons->where('deposit', '>', 0)->count();
        $noneCount    = $persons->where('deposit', '<=', 0)->count();

        $rooms  = Room::orderBy('room_number')->get();
        $cities = Person::whereNotNull('city')->where('city', '!=', '')->distinct()->orderBy('city')->pluck('city');

        return view('reports.deposit', compact(
            'persons', 'rooms', 'cities',
            'totalDeposit', 'paidCount', 'noneCount'
        ));
    }
}
