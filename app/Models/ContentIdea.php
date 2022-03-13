<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentIdea extends Model
{
    use HasFactory;
    protected $guarded =[];
    protected $with = ['external_websites:id,url','internal_websites:id,url'];


    /**
     * Get the external websites.
     */
    public function external_websites()
    {
        return $this->belongsTo(ExternalWebsite::class, 'external_website_id');
    }

    /**
     * Get the internal websites.
     */
    public function internal_websites()
    {
        return $this->belongsTo(Website::class, 'website_id');
    }

}
