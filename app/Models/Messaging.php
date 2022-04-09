<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messaging extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['sender:id,name','recipient:id,name'];

    public function sender() {
        return $this->belongsTo(User::class, 'from_user_id');
    }
    
    public function recipient() {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
