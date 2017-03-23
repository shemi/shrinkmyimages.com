<?php

namespace App\Http\Controllers;


use App\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{

    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'list' => 'required|in:api,wordpress'
        ]);

        /** @var Subscription $subscription */
        $subscription = Subscription::firstOrNew([
            'email' => $request->input('email')
        ]);

        if(in_array($request->input('list'), $subscription->lists)) {
            return $this->respond('exists');
        }


        $lists = $subscription->lists;
        $lists[] = $request->input('list');
        $subscription->lists = $lists;
        $subscription->save();

        return $this->respond('success');
    }

}