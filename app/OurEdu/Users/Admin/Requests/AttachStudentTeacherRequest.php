<?php

namespace App\OurEdu\Users\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachStudentTeacherRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subject_id' => 'required|exists:subjects,id',
            'student_teacher_id' => 'required|exists:users,id'
        ];
    }
}
