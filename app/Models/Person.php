<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'aadhar_number',
        'guardian_name', 'guardian_phone', 'room_id', 'person_type',
        'join_date', 'is_active', 'notes'
    ];

    protected $casts = [
        'join_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    public function getTotalPaidAttribute()
    {
        return $this->fees()->where('status', 'paid')->sum('amount');
    }

    public function getTotalPendingAttribute()
    {
        return $this->fees()->where('status', 'pending')->sum('amount');
    }
}
