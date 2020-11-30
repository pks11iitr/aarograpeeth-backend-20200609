<?php


namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Support\Facades\Storage;

class CustomerDisease extends Model
{
    protected $table = 'customer_disease';

    protected $fillable = ['therapiest_work_id', 'disease_id', 'type'];

    protected $hidden = ['created_at', 'deleted_at', 'updated_at'];


}
