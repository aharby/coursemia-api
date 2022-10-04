<?php

namespace App\OurEdu\GarbageMedia\Requests\Api;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class PostImages extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.images' => 'required|array',
            'attributes.images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
