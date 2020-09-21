<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $table='documents';

    protected $fillable=['entity_type','entity_id', 'file_path', 'file_type'];

    protected $hidden = ['created_at','deleted_at','updated_at','entity_id', 'entity_type'];

    public function entity(){
        return $this->morphTo();
    }

    public function getFilePathAttribute($value){
        if($value)
            return Storage::url($value);
        return '';
    }

}
