<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory, Uuid;
   
    public $incrementing = false;

    protected $keyType = 'uuid';
    protected $guarded = [];
    protected $with = ['payout:id,status','feed_back:id,feedback'];


    /**
     * Get the payout for the is for.
     */
    public function payout()
    {
        return $this->belongsTo(Payout::class);
    }

    /**
     * Get the user the task is assigned to.
     */
    public function assigner_user()
    {
        return $this->belongsTo(User::class,'assigned_to');
    }

    /**
     * Get the user that created the task.
     */
    public function created_by()
    {
        return $this->belongsTo(User::class,'admin_id');
    }

    /**
     * Get the feedback for the task.
     */
    public function feed_back()
    {
        return $this->hasOne(Feedback::class,'task_id');
    }

    
}
