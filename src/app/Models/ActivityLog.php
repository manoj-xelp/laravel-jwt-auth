<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model
{
    use SoftDeletes;

    public $table = 'activity_logs';
    protected $primaryKey = 'id';

    protected $guarded = [];
    
}
