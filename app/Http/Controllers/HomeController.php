<?php

namespace App\Http\Controllers;

use App\Shrink\Repositories\UserStateRepository;
use App\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $state = UserStateRepository::state(auth()->user());

        return view('home', compact('state'));
    }
}
