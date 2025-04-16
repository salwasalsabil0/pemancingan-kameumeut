<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ikan extends Model
{
    use HasFactory;
    protected $table = 'field_ikan';
    protected $fillable = ['field_data_id','type_ikan', 'perkg_stock', 'perkg_price'];

    public function kolam() {
        return $this->belongsTo(FieldData::class, 'field_data_id');
    }
}
