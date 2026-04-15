<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Person;
use App\Models\Room;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Monthly Report
    public function monthly(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $fees = Fee::with('person.room')
            ->where('fee_month', $month)
            ->where('fee_year', $year)
            ->orderBy('status')
            ->get();

        $totalAmount = $fees->sum('amount');
        $totalPaid = $fees->where('status', 'paid')->sum('amount');
        $totalPending = $fees->where('status', 'pending')->sum('amount');

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
        }

        $years = Fee::selectRaw('DISTINCT fee_year')
            ->orderBy('fee_year', 'desc')
            ->pluck('fee_year')
            ->toArray();

        if (empty($years)) {
            $years = [now()->year];
        }

        return view('reports.monthly', compact(
            'fees', 'month', 'year', 'totalAmount',
            'totalPaid', 'totalPending', 'months', 'years'
        ));
    }

    // Quarterly Report
    public function quarterly(Request $request)
    {
        $quarter = $request->get('quarter', ceil(now()->month / 3));
        $year = $request->get('year', now()->year);

        $startMonth = ($quarter - 1) * 3 + 1;
        $endMonth = $quarter * 3;

        $fees = Fee::with('person.room')
            ->where('fee_year', $year)
            ->whereBetween('fee_month', [$startMonth, $endMonth])
            ->orderBy('fee_month')
            ->orderBy('status')
            ->get();

        $totalAmount = $fees->sum('amount');
        $totalPaid = $fees->where('status', 'paid')->sum('amount');
        $totalPending = $fees->where('status', 'pending')->sum('amount');

        // Group by person for summary
        $personSummary = $fees->groupBy('person_id')->map(function ($personFees) {
            $person = $personFees->first()->person;
            return [
                'person' => $person,
                'total' => $personFees->sum('amount'),
                'paid' => $personFees->where('status', 'paid')->sum('amount'),
                'pending' => $personFees->where('status', 'pending')->sum('amount'),
                'months' => $personFees,
            ];
        });

        $years = Fee::selectRaw('DISTINCT fee_year')
            ->orderBy('fee_year', 'desc')
            ->pluck('fee_year')
            ->toArray();

        if (empty($years)) {
            $years = [now()->year];
        }

        return view('reports.quarterly', compact(
            'fees', 'quarter', 'year', 'totalAmount',
            'totalPaid', 'totalPending', 'personSummary', 'years'
        ));
    }

    // Report by Room
    public function byRoom(Request $request)
    {
        $roomId = $request->get('room_id');
        $year = $request->get('year', now()->year);

        $rooms = Room::withCount(['persons' => function ($q) {
            $q->where('is_active', true);
        }])->orderBy('room_number')->get();

        $fees = collect();
        $selectedRoom = null;
        $personSummary = collect();

        if ($roomId) {
            $selectedRoom = Room::find($roomId);
            $persons = Person::where('room_id', $roomId)->where('is_active', true)->get();

            $fees = Fee::with('person')
                ->whereIn('person_id', $persons->pluck('id'))
                ->where('fee_year', $year)
                ->orderBy('fee_month')
                ->get();

            $personSummary = $fees->groupBy('person_id')->map(function ($personFees) {
                $person = $personFees->first()->person;
                return [
                    'person' => $person,
                    'total' => $personFees->sum('amount'),
                    'paid' => $personFees->where('status', 'paid')->sum('amount'),
                    'pending' => $personFees->where('status', 'pending')->sum('amount'),
                ];
            });
        }

        $years = Fee::selectRaw('DISTINCT fee_year')
            ->orderBy('fee_year', 'desc')
            ->pluck('fee_year')
            ->toArray();

        if (empty($years)) {
            $years = [now()->year];
        }

        return view('reports.by_room', compact(
            'rooms', 'fees', 'selectedRoom', 'personSummary',
            'roomId', 'year', 'years'
        ));
    }

    // Report by Person
    public function byPerson(Request $request)
    {
        $personId = $request->get('person_id');
        $year = $request->get('year', now()->year);

        $persons = Person::with('room')->orderBy('name')->get();

        $fees = collect();
        $selectedPerson = null;

        if ($personId) {
            $selectedPerson = Person::with('room')->find($personId);
            $fees = Fee::where('person_id', $personId)
                ->where('fee_year', $year)
                ->orderBy('fee_month')
                ->get();
        }

        $totalAmount = $fees->sum('amount');
        $totalPaid = $fees->where('status', 'paid')->sum('amount');
        $totalPending = $fees->where('status', 'pending')->sum('amount');

        $years = Fee::selectRaw('DISTINCT fee_year')
            ->orderBy('fee_year', 'desc')
            ->pluck('fee_year')
            ->toArray();

        if (empty($years)) {
            $years = [now()->year];
        }

        return view('reports.by_person', compact(
            'persons', 'fees', 'selectedPerson', 'personId',
            'year', 'years', 'totalAmount', 'totalPaid', 'totalPending'
        ));
    }
}
