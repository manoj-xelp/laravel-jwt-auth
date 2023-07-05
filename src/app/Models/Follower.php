<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Follower extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = 'followers';
    protected $primaryKey = 'id';

    protected $guarded = [];

    public function follower()
    {
        return $this->hasOne('App\Models\User', "id", "follower_id");
    }

    public function following()
    {
        return $this->hasOne('App\Models\User', "id", "leader_id");
    }
}
