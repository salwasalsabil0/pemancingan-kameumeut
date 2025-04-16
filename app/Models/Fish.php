<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fish extends Model
{
    use HasFactory;
    protected $table = 'fishes';
    protected $fillable = ['pond_type_id', 'type_ikan', 'perkg_stock', 'perkg_price'];

    public function pondType()
    {
        return $this->belongsTo(PondType::class);
    }
    public function fields()
    {
        return $this->hasMany(Field::class);
    }
    public function bookings()
{
    return $this->hasMany(Booking::class, 'fish_id');
}
}
