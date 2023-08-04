<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'user_tb';
    protected $primaryKey = 'user_id';
    protected $guarded = []; 
    public $timestamps = false;
}