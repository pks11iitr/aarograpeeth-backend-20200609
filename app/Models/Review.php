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

    public function clinic(){
        return $this->belongsTo('App\Models\Clinic', 'entity_id');
    }

    public function therapy(){
        return $this->belongsTo('App\Models\Therapy', 'entity_id');
    }

    public function product(){
        return $this->belongsTo('App\Models\Product', 'entity_id');
    }

    public function user(){
        return $this->belongsTo('App\Models\User', 'entity_id');
    }


}
