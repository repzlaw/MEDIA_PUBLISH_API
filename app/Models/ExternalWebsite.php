<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalWebsite extends Model
{
    use HasFactory;
    protected $guarded =[];
    protected $with = ['region:id,name'];


    /**
     * Get the region the website is for.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
