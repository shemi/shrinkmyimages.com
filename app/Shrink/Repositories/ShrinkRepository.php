<?php

namespace App\Shrink\Repositories;

use App\Shrink;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShrinkRepository
{
    use ValidatesRequests;

    public function create(Request $request, User $user = null)
    {
        $shrink = new Shrink();
        $now = Carbon::now();
        $expire = $now->copy()->addDay();

        if ($user) {
            $shrink->user_id = $user->id;
        }

        $shrink->expire_at = $expire;
        $shrink->mode = $request->input('mode');

        $shrink->save();

        return $shrink;
    }

    public function validateCreateRequest(Request $request)
    {
        $this->validate($request, [
            'mode' => [
                'required',
                Rule::in(['high', 'best', 'small'])
            ]
        ]);
    }



}