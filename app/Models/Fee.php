<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id', 'fee_type', 'amount', 'fee_month', 'fee_year',
        'status', 'paid_date', 'payment_mode', 'receipt_number', 'notes'
    ];

    protected $casts = [
        'paid_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function getMonthNameAttribute()
    {
        return date('F', mktime(0, 0, 0, $this->fee_month, 1));
    }

    public function getQuarterAttribute()
    {
        return ceil($this->fee_month / 3);
    }
}
