<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicConfiguration extends Model
{
    protected $table = 'clinic_configurations';

    protected $fillable = ['clinic_id', 'param_name', 'param_value'];
}
