<?php


namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Support\Facades\Storage;

class Disease extends Model
{
    protected $table = 'disease';

    protected $fillable = ['name', 'isactive', 'avoid_treatment'];

    protected $hidden = ['created_at', 'deleted_at', 'updated_at'];

}

