<?php

namespace App\Http\Controllers;

use App\Shrink\Repositories\UserStateRepository;

class AccountController extends Controller
{

    public function status()
    {
        return $this->respond(UserStateRepository::state(auth()->user()));
    }

}