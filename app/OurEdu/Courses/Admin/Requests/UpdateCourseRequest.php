<?php

namespace App\OurEdu\Courses\Admin\Requests;

use Illuminate\Validation\Rule;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Users\Repository\InstructorRepositoryInterface;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use Carbon\Carbon;

class UpdateCourseRequest extends BaseAppRequest
{
    private $instructotReposatory;

    public function __construct(
    InstructorRepositoryInterface $instructotReposatory )
    {
        $this->instructotReposatory = $instructotReposatory;

    }


    public function rules()
    {
        $rules = [
            'name'  =>  'required',
            'type'  =>  ['required', Rule::in(CourseEnums::getTypes())],
            'subject_id'    =>  [
                'required_if:type,' . CourseEnums::SUBJECT_COURSE,
                'nullable',
                'integer',
                'exists:subjects,id'
            ],
            'subscription_cost' =>  'required|numeric',
            'start_date'    =>  'required|date|after_or_equal:today',
            'end_date'  =>  'required|date|after_or_equal:today',
            'instructor_id' =>  'required|integer|exists:users,id',
            'is_active' =>  'required|boolean',
        ];

        if ($this->route('id')) {
            $rules["picture"] = ['image'];
            $rules["medium_picture"] = ['image'];
            $rules["small_picture"] = ['image'];
            $rules['start_date'] = ['required','date'];
            $rules['end_date'] = ['required','date'];
        } else {
            $rules["picture"] = ['required', 'image'];
            $rules["medium_picture"] = ['required', 'image'];
            $rules["small_picture"] = ['required', 'image'];
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->route('id')) {
            $thisSessions = $this->instructotReposatory->getSessionsCourse($this->route('id'));
            $thisSessions = collect($thisSessions);
            $thisSessions->each(function ($session, $key) use ($thisSessions) {

                $from =  $session['start_time'] ;
                $to   =     $session['end_time'];

                $sessions = $this->instructotReposatory->getInstructorSessions($this->instructor_id);

                $sessions=  $sessions->where('date',  $session['date']);
                $sessions = $sessions->get();
                foreach ($sessions as $key => $createdSession) {
                if ($from >= $createdSession['start_time'] && $from <= $createdSession['end_time']) {
                     $this->addError("instructor_id", trans('validation.you are have a session at the same time',[
                        'from'=> $from,
                        'to' => $to
                    ]));
                   }

               if ($to >= $createdSession['start_time'] && $to <= $createdSession['end_time']) {
                   $this->addError("instructor_id", trans('validation.you are have a session at the same time',[
                    'from'=> $from,
                    'to' => $to
                ]));
                   }

                if ($from <= $createdSession['start_time'] && $to >= $createdSession['end_time']) {

                    $this->addError("instructor_id", trans('validation.you are have a session at the same time',[
                        'from'=> $from,
                        'to' => $to
                    ]));
                   }
                }

                    $schedules = VCRSchedule::query()->where('instructor_id', $this->instructor_id)
                    ->whereDate('from_date','<=',$session['date'])
                    ->whereDate('to_date','>=',$session['date']);

                if (
                    $schedules->wherehas('workingDays', function($dayes)  use ($from , $to){
                        $dayes->whereBetween('from_time', [$from, $to]);

                    })->count()
                    ) {
                        $this->addError("instructor_id", trans('validation.you are have a session at the same time'));
                    }

                    if (
                        $schedules->wherehas('workingDays', function($dayes)  use ($from , $to){
                            $dayes->whereBetween('to_time', [$from, $to]);

                        })->count()
                    ) {
                        $this->addError("instructor_id", trans('validation.you are have a session at the same time'));
                    }

                 });

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
