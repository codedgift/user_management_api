<?php
namespace App\Traits;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * @author Gift Amah
 */

trait Response {
    private function response($status, $message, $status_code, $data) 
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }

    private function validateArray($array, $type, $code) 
    {
        $array['status'] = isset($array['status']) ? $array['status'] : $type;
        $array['status_code'] = isset($array['status_code']) ? $array['status_code'] : $code;
        $array['data'] = isset($array['data']) ? $array['data'] : [];

        return $array;
    }

    public function sendSuccess($array = []) 
    {
        $array = $this->validateArray($array, true, HttpResponse::HTTP_OK);
        return $this->response($array['status'], $array['message'], $array['status_code'], $array['data']);
    }

    public function sendError($array = []) 
    {
        $array = $this->validateArray($array,false, HttpResponse::HTTP_BAD_REQUEST);
        return $this->response($array['status'], $array['message'], $array['status_code'], $array['data']);
    }

    public function internalServerError($e) 
    {
        return $this->response(false, "Whoops..an error occurred", HttpResponse::HTTP_INTERNAL_SERVER_ERROR, []);
    }

    public function internalServerError2($e) 
    {
        return $this->response(false, $e->getMessage(), HttpResponse::HTTP_INTERNAL_SERVER_ERROR, []);
    }

    public function validatorError($validator) 
    {
        return $this->response(false, $validator->errors(), HttpResponse::HTTP_UNPROCESSABLE_ENTITY, []);
    }
}