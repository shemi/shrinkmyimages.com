<?php

namespace App\Shrink\Repositories;

use App\User;

class UserStateRepository
{

    public static function state(User $user = null)
    {
        $state = [
            'webImagesPerShrink' => config('app.web_images_per_shrink'),
            'totalApiPrePaidCredits' => null,
            'user' => null,
            'totalApiUsedCredits' => null
        ];

        /** @var User $user */
        if($user) {
            $state['user'] = $user->toArray();
            $state['totalApiPrePaidCredits'] = $user->balance->total_free_credits;
            $state['totalApiUsedCredits'] = $user->balance->total_used;
            $state['webImagesPerShrink'] = config('app.web_images_per_shrink_member');
        }

        return $state;
    }

}