<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory,Uuid;
    public $incrementing = false;

    protected $table = 'logs';
    protected $keyType = 'uuid';
    protected $guarded = [];
}
