<?php

namespace App\OurEdu\BaseApp\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class BaseApiTokenDataRequest extends FormRequest
{
    public function validationData()
    {
        $data = $this->json()->all();
        if (!isset($data['data']['attributes'])) {
            throw new HttpResponseException(
                response()->json(
                    [
                        'status' => 422,
                        'title' => 'attributes not found',
                        'detail' => 'attributes not found'
                    ],
                    422
                )
            );
        }
        return $data['data']['attributes'];
    }

    public function validationRelationships()
    {
        $data = $this->json()->all();
        if (!isset($data['data']['relationships'])) {
            throw new HttpResponseException(
                response()->json(
                    [
                        'status' => 422,
                        'title' => 'relationships not found',
                        'detail' => 'relationships not found'
                    ],
                    422
                )
            );
        }
        return $data['data']['relationships'];
    }
}
