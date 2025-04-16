<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldData extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // $guarded

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function scheduleAvailabilities()
    {
        return $this->hasMany(ScheduleAvailability::class);
    }
    public function ikan() {
        return $this->belongsTo(Ikan::class, 'field_data_id');
    }

    public function fishes()
    {
        return $this->hasMany(Fish::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
