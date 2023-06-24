<?php

namespace App\Modules\FAQs\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class FAQResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "question_en" => $this->translate('en')->question,
            "question_ar" => $this->translate('ar')->question,
            "answer_en" => $this->translate('en')->answer,
            "answer_ar" => $this->translate('ar')->answer
        ];
    }
}
