<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function balances()
    {
        return $this->hasMany(Balance::class);
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    public function getValidCreditsAttribute()
    {

    }

    public function getBalanceAttribute()
    {
        $now = Carbon::now();

        $balance = $this->balances()
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->first();

        if(! $balance) {
            $balance = Balance::createNewForUser($this);
        }

        return $balance;
    }



}
