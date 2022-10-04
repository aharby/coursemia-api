<?php

namespace App\OurEdu\Subjects\SME\Requests;


use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class UpdateSubjectStructuralRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.section_type' => 'required',
            'attributes.subject_library_text'=> '',
            'attributes.subject_library_attachment.*'=> 'nullable|integer',
            'relationships.subject_format_subjects.data.*.title' => 'required',
            'relationships.subject_format_subjects.data.*.is_active' => 'required|boolean',
        ];


    }
}
