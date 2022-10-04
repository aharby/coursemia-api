<?php

namespace App\OurEdu\PsychologicalTests\Admin\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalRecomendation;

class PsychologicalRecomendationRequest extends BaseAppRequest
{
    public function rules()
    {
        return [
            'result:ar'  =>  'required|string',
            'result:en'  =>  'required|string',
            'recomendation:ar'  =>  'required|string',
            'recomendation:en'  =>  'required|string',
            'from'  =>  'required|numeric|min:0|max:100',
            'to'  =>  'required|numeric|min:0|max:100',
            'is_active' =>  'required|boolean'
        ];
    }

    protected function prepareForValidation()
    {
        // creating case
        if ($this->route('testId')) {
            $recomendations = PsychologicalRecomendation::where('psychological_test_id', $this->route('testId'))->get();

            if ($recomendations->count()) {
                if ($this->to && $this->from) {
                    $this->intesectCheck($recomendations);
                }
            }
        }
        
        // updating case
        if ($this->route('id')) {
            $recomendation = PsychologicalRecomendation::find($this->route('id'));

            $recomendations = PsychologicalRecomendation::where('psychological_test_id', $recomendation->psychological_test_id)->where('id', '!=', $recomendation->id)->get();

            if ($recomendations->count()) {
                if ($this->to && $this->from) {
                    $this->intesectCheck($recomendations);
                }
            }
        }
    }

    protected function intesectCheck($recomendations)
    {
        $range = $this->from > $this->to ? [$this->to, $this->from] : [$this->from, $this->to];

        if ($recomendations->whereBetween('from', $range)->count()) {
            $this->addError("to", trans('validation.Recomendation points intersects with another recomendation in the same test'));
        }

        if ($recomendations->whereBetween('to', $range)->count()) {
            $this->addError("from", trans('validation.Recomendation points intersects with another recomendation in the same test'));
        }
    }
    

    protected function addError($key, $message)
    {
        $validator = $this->getValidatorInstance();

        $validator->after(function ($validator) use ($key, $message) {
            $validator->errors()->add($key, $message);
        });
    }
}
