<?php

namespace App\Models;

use App\Models\Traits\Active;
use Illuminate\Database\Eloquent\Model;

class MainDisease extends Model
{
    use Active;
    protected $table='main_diseases';

    protected $fillable = ['name','isactive', 'recommended_days'];


//    public function reasons(){
//        return $this->hasMany('App\Models\ReasonDisease', 'main_disease_id');
//    }
}
