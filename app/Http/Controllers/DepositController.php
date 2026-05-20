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

    public function export(Request $request)
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

        $filename = 'deposit_report_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        ];

        $callback = function () use ($persons, $totalDeposit) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                '#', 'Name', 'City', 'Room', 'Phone',
                'Type', 'Join Date', 'Status', 'Deposit (₹)',
            ]);

            foreach ($persons as $i => $person) {
                fputcsv($handle, [
                    $i + 1,
                    $person->name,
                    $person->city ?? '',
                    $person->room->room_number ?? '',
                    $person->phone ?? '',
                    ucfirst($person->person_type ?? 'student'),
                    $person->join_date ? $person->join_date->format('Y-m-d') : '',
                    $person->is_active ? 'Active' : 'Inactive',
                    $person->deposit ?? 0,
                ]);
            }

            // Total row
            fputcsv($handle, ['', '', '', '', '', '', '', 'TOTAL', $totalDeposit]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
