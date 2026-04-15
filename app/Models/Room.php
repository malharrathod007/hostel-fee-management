<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_number', 'floor', 'capacity', 'student_rent', 'employee_rent', 'description'];

    public function rentFor(string $personType): float
    {
        return (float) ($personType === 'employee' ? $this->employee_rent : $this->student_rent);
    }

    public function persons()
    {
        return $this->hasMany(Person::class);
    }

    public function getOccupancyAttribute()
    {
        return $this->persons()->where('is_active', true)->count();
    }

    public function getIsFullAttribute()
    {
        return $this->occupancy >= $this->capacity;
    }
}
