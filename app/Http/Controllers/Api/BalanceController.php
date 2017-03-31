<?php

namespace App\Http\Controllers\Api;

use App\Call;
use App\User;
use Illuminate\Http\Request;

class BalanceController extends ApiController
{

    public function check(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $call = new Call();
        $call->user_id = $user->id;
        $call->type = 'BalanceController@check';
        $call->status = 2;
        $call->from_ip = $request->ip();
        $call->credit = 0;
        $call->caller_identifier = $user->token()->id;
        $call->save();

        return [
            'totalShrinks' => $user->balance->total_used,
            'remainingPrePaidShrinks' => $user->balance->remainingFreeCredits()
        ];
    }

}