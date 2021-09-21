<?php


namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Support\Facades\Storage;

class PainPoint extends Model
{
    use Active;

    protected $table = 'pain_point';

    protected $fillable = ['name', 'isactive'];

    protected $hidden = ['created_at', 'deleted_at', 'updated_at'];

}

