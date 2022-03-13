<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    use HasFactory;

    protected $guarded =[];
    protected $with = ['websites:id,url','region:id,name'];

    /**
     * Get the external websites.
     */
    public function websites()
    {
        return $this->hasMany(ExternalWebsite::class, );
    }

    /**
     * Get the region the network is for.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
