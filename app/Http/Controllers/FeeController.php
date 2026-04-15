<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Person;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Fee::with('person.room');

        if ($request->filled('month')) {
            $query->where('fee_month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('fee_year', $request->year);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('person_id')) {
            $query->where('person_id', $request->person_id);
        }

        $fees = $query->orderBy('fee_year', 'desc')
                      ->orderBy('fee_month', 'desc')
                      ->get();

        $persons = Person::where('is_active', true)->orderBy('name')->get();
        $years = Fee::selectRaw('DISTINCT fee_year')->orderBy('fee_year', 'desc')->pluck('fee_year');

        return view('fees.index', compact('fees', 'persons', 'years'));
    }

    public function create(Request $request)
    {
        $persons = Person::where('is_active', true)->with('room')->orderBy('name')->get();
        $rooms = \App\Models\Room::orderBy('room_number')->get();
        $selectedPerson = $request->person_id;
        return view('fees.create', compact('persons', 'rooms', 'selectedPerson'));
    }

    /**
     * Returns true if a fee for $year/$month is on or after the person's join month.
     * Fees cannot be added before the person joined.
     */
    private function feePeriodIsAfterJoin(Person $person, int $year, int $month): bool
    {
        if (!$person->join_date) {
            return true;
        }
        $feeStart  = (int) ($year * 100 + $month);
        $joinStart = (int) ($person->join_date->year * 100 + $person->join_date->month);
        return $feeStart >= $joinStart;
    }

    public function store(Request $request)
    {
        $request->validate([
            'person_id' => 'required|exists:persons,id',
            'amount' => 'required|numeric|min:0',
            'fee_month' => 'required|integer|between:1,12',
            'fee_year' => 'required|integer|min:2000|max:2099',
            'status' => 'required|in:pending,paid,partial',
            'paid_date' => 'nullable|date',
            'payment_mode' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $person = Person::findOrFail($request->person_id);

        // Block fees before the person's join month
        if (!$this->feePeriodIsAfterJoin($person, (int) $request->fee_year, (int) $request->fee_month)) {
            return back()->withErrors([
                'fee_month' => "Cannot add a fee before {$person->name}'s joining month ({$person->join_date->format('F Y')}).",
            ])->withInput();
        }

        // Check for duplicate
        $exists = Fee::where('person_id', $request->person_id)
            ->where('fee_month', $request->fee_month)
            ->where('fee_year', $request->fee_year)
            ->exists();

        if ($exists) {
            return back()->withErrors(['fee_month' => 'Fee entry already exists for this person for the selected month/year.'])->withInput();
        }

        $data = $request->all();
        $data['fee_type'] = $person->person_type ?? 'student';

        Fee::create($data);

        return redirect()->route('fees.index')->with('success', 'Fee entry created successfully!');
    }

    // Generate fees for every active person in a given room (one fee per person, type-aware rate)
    public function storeForRoom(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'fee_month' => 'required|integer|between:1,12',
            'fee_year' => 'required|integer|min:2000|max:2099',
            'status' => 'required|in:pending,paid,partial',
        ]);

        $room = \App\Models\Room::with(['persons' => function ($q) {
            $q->where('is_active', true);
        }])->findOrFail($request->room_id);

        $created = 0;
        $skipped = 0;
        $skippedNotJoined = 0;

        foreach ($room->persons as $person) {
            // Skip persons who have not yet joined by this fee period
            if (!$this->feePeriodIsAfterJoin($person, (int) $request->fee_year, (int) $request->fee_month)) {
                $skippedNotJoined++;
                continue;
            }

            $exists = Fee::where('person_id', $person->id)
                ->where('fee_month', $request->fee_month)
                ->where('fee_year', $request->fee_year)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $type = $person->person_type ?? 'student';
            $amount = $room->rentFor($type);

            Fee::create([
                'person_id'  => $person->id,
                'fee_type'   => $type,
                'amount'     => $amount,
                'fee_month'  => $request->fee_month,
                'fee_year'   => $request->fee_year,
                'status'     => $request->status,
                'paid_date'  => $request->status === 'paid' ? now()->toDateString() : null,
            ]);
            $created++;
        }

        $msg = "Room {$room->room_number}: created {$created} fee(s), skipped {$skipped} (already existed)";
        if ($skippedNotJoined > 0) {
            $msg .= ", skipped {$skippedNotJoined} (not yet joined)";
        }
        return redirect()->route('fees.index')->with('success', $msg . '.');
    }

    public function edit(Fee $fee)
    {
        $persons = Person::where('is_active', true)->with('room')->orderBy('name')->get();
        return view('fees.edit', compact('fee', 'persons'));
    }

    public function update(Request $request, Fee $fee)
    {
        $request->validate([
            'person_id' => 'required|exists:persons,id',
            'amount' => 'required|numeric|min:0',
            'fee_month' => 'required|integer|between:1,12',
            'fee_year' => 'required|integer|min:2000|max:2099',
            'status' => 'required|in:pending,paid,partial',
            'paid_date' => 'nullable|date',
            'payment_mode' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $person = Person::findOrFail($request->person_id);
        if (!$this->feePeriodIsAfterJoin($person, (int) $request->fee_year, (int) $request->fee_month)) {
            return back()->withErrors([
                'fee_month' => "Cannot set a fee before {$person->name}'s joining month ({$person->join_date->format('F Y')}).",
            ])->withInput();
        }

        $fee->update($request->all());

        return redirect()->route('fees.index')->with('success', 'Fee updated successfully!');
    }

    public function destroy(Fee $fee)
    {
        $fee->delete();
        return redirect()->route('fees.index')->with('success', 'Fee entry deleted!');
    }

    // Generate fees for all active persons for a month
    public function generateMonthly(Request $request)
    {
        $request->validate([
            'fee_month' => 'required|integer|between:1,12',
            'fee_year' => 'required|integer|min:2000|max:2099',
        ]);

        $persons = Person::where('is_active', true)->with('room')->get();
        $count = 0;

        foreach ($persons as $person) {
            if (!$this->feePeriodIsAfterJoin($person, (int) $request->fee_year, (int) $request->fee_month)) {
                continue;
            }

            $exists = Fee::where('person_id', $person->id)
                ->where('fee_month', $request->fee_month)
                ->where('fee_year', $request->fee_year)
                ->exists();

            if (!$exists) {
                $type = $person->person_type ?? 'student';
                $amount = $person->room->rentFor($type);

                Fee::create([
                    'person_id' => $person->id,
                    'fee_type'  => $type,
                    'amount'    => $amount,
                    'fee_month' => $request->fee_month,
                    'fee_year'  => $request->fee_year,
                    'status'    => 'pending',
                ]);
                $count++;
            }
        }

        return redirect()->route('fees.index')->with('success', "Generated fees for {$count} persons!");
    }
}
