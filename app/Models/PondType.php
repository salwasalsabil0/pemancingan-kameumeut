<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PondType extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    public function fishes()
    {
        return $this->hasMany(Fish::class);
    }

    public function bookings()
{
    return $this->hasMany(Booking::class);
}
}
