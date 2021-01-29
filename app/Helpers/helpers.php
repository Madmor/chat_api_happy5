<?php

if (!function_exists('user')) {
    /**
     * user.
     *
     */
    function user() {
        return auth('api')->user();
    }
}

if (!function_exists('err_validator')) {
    /**
     * err_validator.
     * $err instance of Illuminate\Support\Facades\Validator
     */
    function err_validator($err) {
        $errors = [];
        foreach ($err as $field => $message) {
            $errors[] = [
                'field'     => $field,
                'message'   => $message[0],
            ];
        }
        return $errors;
    }
}