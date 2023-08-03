<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = 'user_addresses';
    protected $primaryKey = 'id';

    protected $guarded = [];

}
