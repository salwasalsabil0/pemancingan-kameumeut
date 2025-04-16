<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'pond_type_id', 'morning_price',
        'night_price','thumbnail'];
    
    
    protected $guarded = ['id']; // $guarded

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function pondType()
    {
        return $this->belongsTo(PondType::class, 'pond_type_id');
    }
    public function fishes()
    {
        return $this->hasMany(Fish::class, 'pond_type_id', 'pond_type_id');
    }
    public function scheduleAvailabilities()
    {
        return $this->hasMany(ScheduleAvailability::class);
    }
}
