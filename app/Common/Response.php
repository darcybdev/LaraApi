<?php

namespace App\Common;

use Illuminate\Http\JsonResponse;

class Response
{

    public function __construct()
    {

    }

    /**
     * Returns a generic successful JSON response
     * Item response should just return a JSON object
     * Collection response should return an array of JSON objects
     * Any needed metadata should be set in headers
     */
    public function data($data, $status = 200, array $headers = [], $options = 0)
    {
        return new JsonResponse($data, $status, $headers, $options);
    }

    /**
     * Returns an error response
     */
    public function error($errors, $status = 422, array $headers = [], $options = 0)
    {
        $formatted = [];
        foreach ($errors as $key => $values) {
            $formatted[] = [
                'key' => $key,
                'messages' => $values
            ];
        }
        return new JsonResponse([
            'errors' => $formatted
        ], $status, $headers, $options);
    }

    /**
     * Returns a not found error response
     */
    public function notFound($message = 'Not found')
    {
        return new JsonResponse([
            'errors' => [
                $message
            ]
        ], 404);
    }

    /**
     * Returns an unauthorized response
     */
    public function notAllowed()
    {
        return new JsonResponse([
            'errors' => [
                'Unauthorized'
            ]
        ], 401);
    }
}
