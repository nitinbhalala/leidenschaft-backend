<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseController extends Controller
{
    protected function success($data = null, $message = "Success", $status = 200, $admin = false)
    {
        if ($data instanceof LengthAwarePaginator) {
            if ($admin) {
                $data = [
                    'current_page' => $data->currentPage(),
                    'per_page'     => $data->perPage(),
                    'total'        => $data->total(),
                    'data'         => $data->items(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $status);
    }

    protected function error($message = "Error", $status = 400, $data = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $status);
    }
}
