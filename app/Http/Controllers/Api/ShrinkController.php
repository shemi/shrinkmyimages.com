<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShrinkController extends Controller
{

    public function shrink(Request $request)
    {
        $user = $request->user();

        if(! $user) {
            return $this->respondNotAuthorized();
        }

        //make shure the user have credit
        //create shrink
        //save file
        //shrink
        //if success shrink eg > 2% less file size
        //charge the user
    }

    public function bulk()
    {

    }

}