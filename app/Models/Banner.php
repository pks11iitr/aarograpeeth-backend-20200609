<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $table='banners';

    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }
}