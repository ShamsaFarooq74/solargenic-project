<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function sendResponse($status, $message, $result)
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'result' => $result
        ];
        return response()->json($response, 200);
    }


    public function sendError($status, $error, $debug = null, $code = 404)
    {
        $response = [
            'status' => $status,
            'message' => $error,
            // 'debug' => $debug
        ];

        return response()->json($response, $code);
    }
}
