<?php
namespace App\Http\Traits;

use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

/**
 * @author Daniel Ozeh hello@danielozeh.com.ng
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
        $array = $this->validateArray($array, true, HttpFoundationResponse::HTTP_OK);
        return $this->response($array['status'], $array['message'], $array['status_code'], $array['data']);
    }

    public function sendError($array = []) 
    {
        $array = $this->validateArray($array,false, HttpFoundationResponse::HTTP_BAD_REQUEST);
        return $this->response($array['status'], $array['message'], $array['status_code'], $array['data']);
    }

    public function internalServerError($e) 
    {
        return $this->response(false, "Whoops..an error occurred", HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR, []);
    }

    public function internalServerError2($e) 
    {
        return $this->response(false, $e->getMessage(), HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR, []);
    }

    public function validatorError($validator) 
    {
        return $this->response(false, $validator->errors(), HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY, []);
    }
}