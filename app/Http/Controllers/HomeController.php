<?php

namespace App\Http\Controllers;

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
        $state = [
            'webImagesPerShrink' => config('app.web_images_per_shrink'),
            'totalApiPrePaidCredits' => null,
            'user' => null,
            'totalApiUsedCredits' => null
        ];

        /** @var User $user */
        if($user = auth()->user()) {
            $state['user'] = $user->toArray();
            $state['totalApiPrePaidCredits'] = $user->balance->total_free_credits;
            $state['totalApiUsedCredits'] = $user->balance->total_used;
            $state['webImagesPerShrink'] = config('app.web_images_per_shrink_member');
        }

        return view('home', compact('state'));
    }
}
