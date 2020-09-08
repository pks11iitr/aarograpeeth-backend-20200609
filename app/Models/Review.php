<?php

namespace App\Models;

use App\Models\Traits\Active;
use App\Models\BaseModel as Model;

class Review extends Model
{
    use Active;

    protected $table='reviews';

    protected $fillable=['description', 'entity_type', 'entity_id', 'rating', 'user_id', 'order_id', 'session_id'];

    protected $hidden=['id', 'updated_at','deleted_at', 'user_id', 'isactive','entity_type', 'entity_id', 'order_id'];

    public function entity(){
        return $this->morphTo();
    }

    public function customer(){
        return $this->belongsTo('App\Models\Customer', 'user_id');
    }

}
