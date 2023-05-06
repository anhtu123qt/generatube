<?php

namespace App\Http\Controllers;

use App\Http\Traits\Responsable;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    public function response(int $statusCode, array $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $statusCode);
    }
    /**
     * return value after checking value is empty or not
     *
     * @param mixed $value
     * @return mixed
     */
    public function getValue(mixed $value): mixed
    {
        if (empty($value)) {

            return $this->returnType($value);
        }

        return $value;
    }

    /**
     * return [] or '' by type of value
     *
     * @param mixed $value
     * @return string|array
     */
    private function returnType(mixed $value) : string|array
    {
        if (gettype($value) == 'array') {

            return [];
        }

        return '';
    }
}
