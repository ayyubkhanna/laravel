<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function httpResponse($status, $message, $data, $codeStatus)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $codeStatus);
    }
    public function httpResponseError($status, $message, $errors, $codeStatus)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'errors' => $errors
        ], $codeStatus);
    }
}
