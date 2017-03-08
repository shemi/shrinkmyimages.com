<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Balance extends Model
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function createNewForUser(User $user)
    {
        $balance = new static();
        $balance->user_id = $user->id;
        $balance->save();

        return $balance;
    }



}
