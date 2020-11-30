<?php


namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Support\Facades\Storage;

class CustomerPainpoint extends Model
{
    protected $table = 'Customer_point_pain';

    protected $fillable = ['therapiest_work_id', 'pain_point_id', 'related_rating','type'];

    protected $hidden = ['created_at', 'deleted_at', 'updated_at'];

    public function painpoint(){
        return $this->belongsTo('App\Models\PainPoint', 'pain_point_id');
    }
}
