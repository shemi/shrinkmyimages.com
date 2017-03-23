<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class ShrinkController extends ApiController
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