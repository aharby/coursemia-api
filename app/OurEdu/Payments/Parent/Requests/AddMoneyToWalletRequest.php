<?php

namespace App\OurEdu\Payments\Parent\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Payments\Enums\PaymentEnums;
use Illuminate\Validation\Rule;

class AddMoneyToWalletRequest extends BaseApiParserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules =  [
            'attributes.amount' => 'required|numeric|max:100000|min:1',
            'attributes.payment_for' => [
                'string',
                Rule::in(array_keys(PaymentEnums::PRODUCTS_MAP))
            ],
            'attributes.payment_for_id' => 'required_with:attributes.payment_for',
        ];
//        if (!empty($this->getData()['attributes']['payment_for']) &&
//            $this->getData()['attributes']['payment_for'] == PaymentEnums::SUBJECT){
//            $rules['attributes.amount'] .='|min:0';
//        }else{
//            $rules['attributes.amount'] .='|min:1';
//        }
        return $rules;
    }
}
