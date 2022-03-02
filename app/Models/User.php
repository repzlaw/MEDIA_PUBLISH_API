<?php

namespace App\Models;

use App\Traits\Uuid;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Uuid, Notifiable;
    public $incrementing = false;
    protected $appends = array('OnlineStatus');

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_seen',
        'type',
        'payout_per_word',
        'country',
        'bank_details',
        'fixed_monthly_payout',
        'total_payout',
        'currency',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getOnlineStatusAttribute()
    {
        if(Cache::has('user-is-online-' . $this->id))
            return 'online';
        else
            return 'offline';
        
    }
}
