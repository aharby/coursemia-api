<?php

namespace App\OurEdu\Payments\Parent\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Payments\Enums\PaymentEnums;
use Illuminate\Validation\Rule;

class SubscribeByIAPRequest extends BaseApiParserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.payment_for' => [
                'string',
                Rule::in(array_keys(PaymentEnums::PRODUCTS_MAP))
            ],
            'attributes.payment_for_id' => 'required_with:attributes.payment_for',
        ];
    }
}
