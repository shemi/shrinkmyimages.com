<?php

namespace App\Http\Controllers\Api;

use App\Call;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Response;

class ApiController extends Controller
{

    public function respondNoEnoughCredit()
    {
        return $this->respond([
            'message' => 'No enough credit',
        ]);
    }

    public function respond($data, $headers = [], $resultCode = 'ok')
    {
        $user = auth()->user();

        $defaultData = [
            'shrinksCount' => $user->balance->total_used,
            'remainingPrePaidShrinks' => $user->balance->remainingFreeCredits(),
            'code' => $this->getStatusCode()
        ];

        $data = array_merge($defaultData, $data);

        return Response::json($data, $this->getStatusCode(), $headers);
    }

    protected function createCallModel(Request $request, User $user, $shrinkId, $credit, $action = 'shrink')
    {
        $controller = class_basename(get_class($this));

        $call = new Call();
        $call->user_id = $user->id;
        $call->shrink_id = $shrinkId;
        $call->type = "{$controller}@{$action}";
        $call->status = 2;
        $call->from_ip = $request->ip();
        $call->credit = $credit;
        $call->caller_identifier = $user->token()->id;
        $call->save();

        return $call;
    }

}