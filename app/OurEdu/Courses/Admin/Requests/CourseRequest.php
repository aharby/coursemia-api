<?php

namespace App\OurEdu\Courses\Admin\Requests;

use Illuminate\Validation\Rule;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Users\Repository\InstructorRepositoryInterface;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use Carbon\Carbon;

class CourseRequest extends BaseAppRequest
{
    private $instructotReposatory;
    private $scheduleReposatory;

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
//            'is_top_qudrat' =>  'required|boolean',
        ];

        if ($this->sessions) {
            foreach ($this->sessions as $key => $session) {
                $rules["sessions.{$key}.content"]    = "required";
                $rules["sessions.{$key}.date"]    = "required|date|after_or_equal:start_date|before:end_date";
                $rules["sessions.{$key}.start_time"] = ["required", "date_format:H:i:s","before:sessions.{$key}.end_time" ];
                $rules["sessions.{$key}.end_time"] = ["required", "date_format:H:i:s","after:sessions.{$key}.start_time"];
                if(Carbon::parse($session['date'])->toDateString() == Carbon::today()->toDateString()){
                  $rules["sessions.{$key}.start_time"][] = 'after:'.Carbon::now()->addMinute(3)->format('H:i:s');
                }
            }
        }

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

        if ($this->sessions) {
            $thisSessions = collect($this->sessions);

            $thisSessions->each(function ($session, $key) use ($thisSessions) {


                $from =  Carbon::parse($session['start_time'])->format('h:i:s') ;
                $to=   Carbon::parse($session['end_time'])->format('h:i:s');

                if (
                $thisSessions->where('date', $session['date'])->whereBetween('start_time', [$session['start_time'], $session['end_time']])->count() > 1
                ) {
                    $this->addError("sessions.{$key}.end_time", trans('validation.Session time intersects with another session in the same day'));
                }

                if (
                    $thisSessions->where('date', $session['date'])->whereBetween('end_time', [$session['start_time'], $session['end_time']])->count() > 1
                ) {
                    $this->addError("sessions.{$key}.start_time", trans('validation.Session time intersects with another session in the same day'));
                }

                $sessions = $this->instructotReposatory->getInstructorSessions($this->instructor_id);

                $sessions=  $sessions->where('date',  $session['date']);
                if (
                    $sessions->whereBetween('start_time', [$from, $to])->count()
                    ) {
                        $this->addError("sessions.{$key}.start_time", trans('validation.Session time intersects with another session in the same day'));
                        $this->addError("sessions.{$key}.end_time", trans('validation.Session time intersects with another session in the same day'));
                    }

                    if (
                        $sessions->whereBetween('end_time', [$from, $to])->count() 
                    ) {
                        $this->addError("sessions.{$key}.start_time", trans('validation.Session time intersects with another session in the same day'));
                        $this->addError("sessions.{$key}.end_time", trans('validation.Session time intersects with another session in the same day'));
                    }


                    $schedules = VCRSchedule::query()->where('instructor_id', $this->instructor_id)
                    ->whereDate('from_date','<=',$session['date'])
                    ->whereDate('to_date','>=',$session['date']);

                if (
                    $schedules->wherehas('workingDays', function($dayes)  use ($from , $to){
                        $dayes->whereBetween('from_time', [$from, $to]);

                    })->count()
                    ) {
                        $this->addError("sessions.{$key}.start_time", trans('validation.Session time intersects with another session in the same day'));
                        $this->addError("sessions.{$key}.end_time", trans('validation.Session time intersects with another session in the same day'));
                    }

                    if (
                        $schedules->wherehas('workingDays', function($dayes)  use ($from , $to){
                            $dayes->whereBetween('to_time', [$from, $to]);

                        })->count()
                    ) {
                        $this->addError("sessions.{$key}.start_time", trans('validation.Session time intersects with another session in the same day'));
                        $this->addError("sessions.{$key}.end_time", trans('validation.Session time intersects with another session in the same day'));
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
