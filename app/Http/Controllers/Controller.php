<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $status_code = 200;

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * @param int $status_code
     * @return $this
     */
    public function setStatusCode($status_code)
    {
        $this->status_code = $status_code;

        return $this;
    }


    public function respondNotFound($message = 'Not found.')
    {
        return $this->setStatusCode(404)->respondWithError($message);
    }



    public function respondNotAuthorized($message = 'not Authorized.')
    {
        return $this->setStatusCode(401)->respondWithError($message);
    }



    public function respondInternalError($message = 'Internal error.')
    {
        return $this->setStatusCode(500)->respondWithError($message);
    }

    public function respondBadRequest($message = 'Bad Request')
    {
        return $this->setStatusCode(400)->respondWithError($message);
    }

    public function respondWithError($message, $resultCode = 'err')
    {

        return Response::json([
            'message' => $message,
            'resultCode' => $resultCode,
            'code'    => $this->getStatusCode()
        ], $this->getStatusCode());
    }



    public function respond($data, $headers = [], $resultCode = 'ok')
    {
        $data = [
            'data' => $data,
            'resultCode' => $resultCode
        ];

        return Response::json($data, $this->getStatusCode(), $headers);
    }
}
