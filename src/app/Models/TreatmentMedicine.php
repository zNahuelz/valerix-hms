<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentMedicine extends Model
{
    protected $fillable = [
        'medicine_id',
        'treatment_id',
    ];
}
