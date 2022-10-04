<?php


namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use Carbon\Carbon;

class UpdateCourseHomeworkRequest extends BaseApiParserRequest
{

    public function rules()
    {
        $rules = [
            'attributes.random_question' => 'required|boolean',
            'attributes.title' => 'required|string',
        ];

        $rules['attributes.end_at'] = 'required|date_format:"Y-m-d H:i:s|after:attributes.start_at';

        if ($homework = $this->route('courseHomework') and empty($homework->published_at)) {
                $rules['attributes.start_at'] = 'required|date|before:attributes.end_at|after:' . now();
        }
        return $rules;
    }
}
