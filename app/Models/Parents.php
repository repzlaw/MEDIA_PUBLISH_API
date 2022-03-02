<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    use HasFactory;
    protected $table = 'parents';
    protected $guarded =[];
    protected $with = ['websites'];


    /**
     * Get the websites.
     */
    public function websites()
    {
        return $this->hasMany(Website::class, 'parent_id');
    }
}
