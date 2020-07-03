<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $table='complaints';

 public function customer(){
        return $this->belongsTo('App\Models\Customer', 'user_id');
    }
    public function messsages(){
        return $this->hasMany('App\Models\ComplainMessage', 'complaint_id');
    }
}
