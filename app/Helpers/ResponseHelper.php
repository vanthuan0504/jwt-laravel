<?php
namespace App\Helpers;

class ResponseHelper
{
    /**
     * Helper Function: jsonResponse
     *
     * This function generates a JSON response with a specified status, message, data, and HTTP status code.
     * It simplifies the process of returning consistent JSON responses in Laravel controllers or other parts of the application.
     *
     * @param bool   $status      The status of the response (true for success, false for failure).
     * @param string $message     The message to be included in the response.
     * @param object $data        The result that the controller returns.
     * @param int    $statusCode  The HTTP status code for the response.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the specified status, message, and HTTP status code.
     */
    public static function jsonResponse($status, $message, $data, $statusCode)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
}
