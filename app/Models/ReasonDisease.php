<?php

namespace App\Models;

use App\Models\Traits\Active;
use Illuminate\Database\Eloquent\Model;

class ReasonDisease extends Model
{
    use Active;
    protected $table='reason_diseases';

    protected $fillable = ['main_disease_id','name','isactive'];
}
