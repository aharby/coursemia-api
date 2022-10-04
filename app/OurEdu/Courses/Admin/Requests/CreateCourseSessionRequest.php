<?php

namespace App\OurEdu\Courses\Admin\Requests;

use Carbon\Carbon;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\Users\Repository\InstructorRepositoryInterface;

class CreateCourseSessionRequest extends BaseAppRequest
{
    private $instructotReposatory;
    private $course;
    public function __construct(
    InstructorRepositoryInterface $instructotReposatory )
    {
        $this->instructotReposatory = $instructotReposatory;

    }


    public function rules()
    {
        if ($this->sessions) {
            foreach ($this->sessions as $key => $session) {
                $rules["sessions.{$key}.content"]    = "required";
                $rules["sessions.{$key}.date"]    = "required|date|after_or_equal:today";
                $rules["sessions.{$key}.start_time"] = ["required", "date_format:H:i:s","before:sessions.{$key}.end_time" ];
                $rules["sessions.{$key}.end_time"] = ["required", "date_format:H:i:s","after:sessions.{$key}.start_time"];
                if(Carbon::parse($session['date'])->toDateString() == Carbon::today()->toDateString()){
                  $rules["sessions.{$key}.start_time"][] = 'after:'.Carbon::now()->addMinute(3)->format('H:i:s');
                }
            }
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
         $this->course = Course::findOrFail($this->route('id'));
         
        if ($this->sessions) {
            $thisSessions = collect($this->sessions);

            $thisSessions->each(function ($session, $key) use ($thisSessions) {


                $from =  $session['start_time'] ;
                $to   =     $session['end_time'];
                
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

                $sessions = $this->instructotReposatory->getInstructorSessions($this->course->instructor_id);
                
                $sessions=  $sessions->where('date',  $session['date']);
                $sessions = $sessions->get();
               
                foreach ($sessions as $key => $createdSession) {
                if ($from >= $createdSession['start_time'] && $from <= $createdSession['end_time']) {
                    $this->addError("sessions.{$key}.start_time", trans('validation.Session time intersects with another session in the same day'));
                    $this->addError("sessions.{$key}.end_time", trans('validation.Session time intersects with another session in the same day'));
                   }

               if ($to >= $createdSession['start_time'] && $to <= $createdSession['end_time']) {
                    $this->addError("sessions.{$key}.start_time", trans('validation.Session time intersects with another session in the same day'));
                    $this->addError("sessions.{$key}.end_time", trans('validation.Session time intersects with another session in the same day'));
                   }

                if ($from <= $createdSession['start_time'] && $to >= $createdSession['end_time']) {
               
                    $this->addError("sessions.{$key}.start_time", trans('validation.Session time intersects with another session in the same day'));
                    $this->addError("sessions.{$key}.end_time", trans('validation.Session time intersects with another session in the same day'));
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
