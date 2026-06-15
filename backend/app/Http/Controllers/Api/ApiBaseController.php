<?php

/* [NEW] [ApiBaseController.php](file:///c:/Users/ASUS/Downloads/rollback_backups/backend/app/Http/Controllers/Api/ApiBaseController.php) */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiBaseController extends Controller
{
    /**
     * Send a success response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $code
     */
    protected function sendResponse($data, $message, $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Send an error response.
     *
     * @param  string  $error
     * @param  array  $errorMessages
     * @param  int  $code
     */
    protected function sendError($error, $errorMessages = [], $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (! empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
