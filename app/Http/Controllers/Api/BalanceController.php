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
        $this->createCallModel($request, $user, null, 0, 'check');

        return $this->respond([]);
    }

}