<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class session extends Model
{
    use HasFactory;
    protected $table = 'session';
    protected $fillable = [
        'session' ,'userid' , 'username'
    ];
    public $timestamps = false;
}
