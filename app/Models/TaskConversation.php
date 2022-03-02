<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskConversation extends Model
{
    use HasFactory, Uuid;
   
    public $incrementing = false;
    protected $guarded = [];
    protected $with = ['user:id,name'];

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->diffForHumans();
    }

    /**
     * Get the user the convo is for.
     */
    public function user()
    {
        return $this->belongsTo(User::class,'sender_id');
    }
    /**
     * Get the task the convo is for.
     */
    public function task()
    {
        return $this->belongsTo(Task::class,'task_id');
    }
}
