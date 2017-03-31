<?php

namespace App\Shrink\Repositories;

use App\User;
use Carbon\Carbon;

class UserStateRepository
{

    public static function state(User $user = null)
    {
        $now = Carbon::now();
        $endOfMonth = $now->copy()->endOfMonth();

        $state = [
            'webImagesPerShrink' => config('app.web_images_per_shrink'),
            'totalApiPrePaidCredits' => null,
            'user' => null,
            'countResetHumans' => null,
            'countResetDate' => null,
            'totalApiUsedCredits' => null
        ];

        /** @var User $user */
        if($user) {
            $state['user'] = static::transformUser($user);
            $state['totalApiPrePaidCredits'] = $user->balance->total_free_credits;
            $state['totalApiUsedCredits'] = $user->balance->total_used;
            $state['webImagesPerShrink'] = config('app.web_images_per_shrink_member');
            $state['countResetHumans'] = $now->diffForHumans($endOfMonth, true);
            $state['countResetDate'] = $endOfMonth->format('l jS \of F Y h:i A');
        }

        return $state;
    }

    public static function transformUser(User $user)
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name
        ];
    }

}