<?php
namespace App\Traits;

/**
 * [Description Response]
 */
trait Response
{
    /**
     * Show success message response
     * @param mixed $error
     * @param mixed $message
     * @param mixed $data
     * @param mixed $status
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function success($error, $message, $data)
    {
        return response()->json([
            "error" => $error,
            "message" => $message,
            "data" => $data,
        ], 200);
    }

    /**
     * Show error message response
     * @param mixed $error
     * @param mixed $message
     * @param mixed $data
     * @param mixed $status
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fail($error, $message, $data = '')
    {
        return response()->json([
            "error" => $error,
            "message" => $message,
            "data" => $data,
        ], 400);
    }
}
