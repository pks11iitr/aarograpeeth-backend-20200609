<?php

namespace App\Models;

use App\Models\Traits\Active;
use Illuminate\Database\Eloquent\Model;

class DiagnosePoint extends Model
{

    use Active;

    protected $table = 'diagnose_points';


    protected $fillable = ['name','type','isactive'];
}
