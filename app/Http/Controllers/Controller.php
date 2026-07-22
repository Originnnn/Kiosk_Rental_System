<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Trả về response thành công chuẩn hóa
     */
    protected function respondSuccess($data = [], $code = 200)
    {
        return response()->json([
            'success' => true,
            'data'    => $data,
            'error'   => null,
        ], $code);
    }

    /**
     * Trả về response báo lỗi chuẩn hóa
     */
    protected function respondError($message, $code = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'data'    => null,
            'error'   => [
                'message' => $message,
                'details' => $errors
            ],
        ], $code);
    }
}