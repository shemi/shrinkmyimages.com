<?php

namespace App\Http\Controllers\Api;

use App\User;

class BalanceController extends ApiController
{

    public function check()
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            'totalShrinks' => $user->balance->total_used,
            'remainingPrePaidShrinks' => $user->balance->remainingFreeCredits()
        ];
    }

}