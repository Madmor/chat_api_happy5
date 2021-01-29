<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
	public $response;
    public $code;

	/**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->response = new \stdClass();
        $this->response->success = true;
        $this->response->error = [];
        $this->response->data = [];
        $this->response->message = '';
        $this->code = 200;
    }

    public function response_api()
    {
        return response()->json($this->response,$this->code);
    }
}
