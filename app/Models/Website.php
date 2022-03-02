<?php

namespace App\Models;

use App\Models\Region;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Website extends Model
{
    use HasFactory;

    protected $guarded =[];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    // protected $with = ['region','parent'];

    /**
     * Get the region the website is for.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the parent the website is for.
     */
    public function parent()
    {
        return $this->belongsTo(Parents::class);
    }
}
