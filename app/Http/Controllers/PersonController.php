<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersonController extends Controller
{
    public function index(Request $request)
    {
        $query = Person::with('room');

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
                  ->orWhere('aadhar_number', 'like', "%{$search}%");
            });
        }

        $persons = $query->orderBy('name')->get();
        $rooms = Room::orderBy('room_number')->get();

        return view('persons.index', compact('persons', 'rooms'));
    }

    public function create(Request $request)
    {
        $rooms = Room::orderBy('room_number')->get();
        $selectedRoom = $request->room_id;
        return view('persons.create', compact('rooms', 'selectedRoom'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('persons', 'email')],
            'phone' => ['required', 'digits:10', 'regex:/^[6-9]\d{9}$/', Rule::unique('persons', 'phone')],
            'address' => 'nullable|string',
            'aadhar_number' => ['required', 'digits:12', Rule::unique('persons', 'aadhar_number')],
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => ['nullable', 'digits:10', 'regex:/^[6-9]\d{9}$/'],
            'room_id' => 'required|exists:rooms,id',
            'person_type' => 'required|in:student,employee',
            'join_date' => 'required|date',
            'notes' => 'nullable|string',
        ], [
            'phone.digits' => 'Phone number must be exactly 10 digits.',
            'phone.regex' => 'Enter a valid Indian mobile number (must start with 6, 7, 8, or 9).',
            'phone.unique' => 'This phone number is already registered with another person.',
            'email.unique' => 'This email is already registered with another person.',
            'aadhar_number.digits' => 'Aadhar number must be exactly 12 digits.',
            'aadhar_number.unique' => 'This Aadhar number is already registered with another person.',
            'guardian_phone.digits' => 'Guardian phone must be exactly 10 digits.',
            'guardian_phone.regex' => 'Enter a valid Indian guardian mobile number.',
        ]);

        // Check room capacity
        $room = Room::findOrFail($request->room_id);
        $currentOccupancy = $room->persons()->where('is_active', true)->count();
        if ($currentOccupancy >= $room->capacity) {
            return back()->withErrors(['room_id' => 'This room is already full!'])->withInput();
        }

        $data = $request->all();
        $data['is_active'] = true;

        Person::create($data);

        return redirect()->route('persons.index')->with('success', 'Person added successfully!');
    }

    public function show(Person $person)
    {
        $person->load(['room', 'fees' => function ($q) {
            $q->orderBy('fee_year', 'desc')->orderBy('fee_month', 'desc');
        }]);

        return view('persons.show', compact('person'));
    }

    public function edit(Person $person)
    {
        $rooms = Room::orderBy('room_number')->get();
        return view('persons.edit', compact('person', 'rooms'));
    }

    public function update(Request $request, Person $person)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('persons', 'email')->ignore($person->id)],
            'phone' => ['required', 'digits:10', 'regex:/^[6-9]\d{9}$/', Rule::unique('persons', 'phone')->ignore($person->id)],
            'address' => 'nullable|string',
            'aadhar_number' => ['required', 'digits:12', Rule::unique('persons', 'aadhar_number')->ignore($person->id)],
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => ['nullable', 'digits:10', 'regex:/^[6-9]\d{9}$/'],
            'room_id' => 'required|exists:rooms,id',
            'person_type' => 'required|in:student,employee',
            'join_date' => 'required|date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ], [
            'phone.digits' => 'Phone number must be exactly 10 digits.',
            'phone.regex' => 'Enter a valid Indian mobile number (must start with 6, 7, 8, or 9).',
            'phone.unique' => 'This phone number is already registered with another person.',
            'email.unique' => 'This email is already registered with another person.',
            'aadhar_number.digits' => 'Aadhar number must be exactly 12 digits.',
            'aadhar_number.unique' => 'This Aadhar number is already registered with another person.',
            'guardian_phone.digits' => 'Guardian phone must be exactly 10 digits.',
            'guardian_phone.regex' => 'Enter a valid Indian guardian mobile number.',
        ]);

        $person->update($request->all());

        return redirect()->route('persons.show', $person)->with('success', 'Person updated successfully!');
    }

    public function destroy(Person $person)
    {
        $person->delete();
        return redirect()->route('persons.index')->with('success', 'Person removed successfully!');
    }
}
