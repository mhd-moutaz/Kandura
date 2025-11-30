<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function success($data, $message = "Success", $code = 200){
        return response()->json([
            "success" => true,
            "message" => $message,
            "data" => $data,
            "timestamp" => gmdate('Y-m-d\TH:i:s\Z'),
        ], $code);

    }
}
