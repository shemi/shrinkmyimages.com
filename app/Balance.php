<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Balance
 *
 * @property int $id
 * @property int $user_id
 * @property int $total
 * @property int $reserved
 * @property bool $it_calculated
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Credit[] $credits
 * @property-read int $total_free_credits
 * @property-read int $total_used
 * @property-read Collection $valid_credits
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Query\Builder|\App\Balance whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Balance whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Balance whereItCalculated($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Balance whereReserved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Balance whereTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Balance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Balance whereUserId($value)
 * @mixin \Eloquent
 */

class Balance extends Model
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function credits()
    {
        return $this->hasManyThrough(Credit::class, User::class);
    }

    public static function createNewForUser(User $user)
    {
        $balance = new static();
        $balance->user_id = $user->id;
        $balance->save();

        return $balance;
    }

    /**
     * @return int
     */
    public function getTotalUsedAttribute()
    {
        return $this->total + $this->reserved;
    }

    /**
     * @return Collection
     */
    public function getValidCreditsAttribute()
    {
        return $this
            ->credits()
            ->whereDate('expired_at', '>=', Carbon::now()->toDateString())
            ->orderBy('expired_at', 'desc')
            ->get();
    }

    /**
     * @return int
     */
    public function getTotalFreeCreditsAttribute()
    {
        $credits = $this->valid_credits->pluck('amount');

        return  ($credits->isEmpty() ? 0 : $credits->sum()) + config('app.free_api_shrinks');
    }

    public function haveFreeCredits()
    {
        return $this->remainingFreeCredits() > 0;
    }

    public function remainingFreeCredits()
    {
        $balance = $this->total_free_credits - $this->total_used;

        return $balance <= 0 ? 0 : $balance;
    }

    /**
     * @return int
     */
    public function shrinksToCharge()
    {
        if($this->haveFreeCredits()) {
            return 0;
        }

        return $this->total - $this->total_free_credits;
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function addReserved($amount = 1)
    {
        $this->reserved += $amount;

        return $this;
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function subtractReserved($amount = 1)
    {
        if($this->reserved > 0) {
            $this->reserved -= $amount;
        }

        return $this;
    }

    public function chargeReserved()
    {
        $this->total += $this->reserved;
        $this->reserved = 0;

        return $this;
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function addTotal($amount = 1)
    {
        $this->total += $amount;

        return $this;
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function subtractTotal($amount = 1)
    {
        if($this->total > 0) {
            $this->total -= $amount;
        }

        return $this;
    }

}
