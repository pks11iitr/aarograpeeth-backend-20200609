<?php

namespace App\Models;

use App\Models\Traits\Active;
use Illuminate\Database\Eloquent\Model;

class DiseasewiseTreatment extends Model
{
    use Active;
    protected $table = 'diseasewise_treatments';


    protected $fillable=['main_disease_id', 'description', 'exercise', 'dont_exercise', 'diet','recommended_days', 'action_when_pain_increase', 'isactive'];

    public function mainDisease(){
        return $this->belongsTo('App\Models\MainDisease',  'main_disease_id');
    }

    public function reasonDiseases(){
        return $this->belongsToMany('App\Models\ReasonDisease', 'treatment_reason_diseases_map', 'treatment_id', 'reason_disease_id');
    }


    public function painPoints(){
        return $this->belongsToMany('App\Models\PainPoint', 'treatment_painpoint_map', 'treatment_id', 'painpoint_id');
    }

    public function ignoreWhenDiseases(){
        return $this->belongsToMany('App\Models\Disease', 'treatment_ignore_disease_map', 'treatment_id', 'disease_id');
    }



}
