<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'customer_name',
        'is_member',
        'discount',
        'total_subtotal',
        'booking_status',
        'field_id',
        'fish_id',
        'pond_type_id',
        'weight',
    ];

    // Relasi ke Fish
    public function fish()
{
    return $this->belongsTo(Fish::class, 'fish_id');
}


    // Relasi ke Pondtype
    public function pondtype()
    {
        return $this->belongsTo(Pondtype::class, 'pond_type_id');
    }
    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'booking_id');
    }

    public function scheduleAvailabilities()
    {
        return $this->hasMany(ScheduleAvailability::class, 'booking_id');
    }
}
