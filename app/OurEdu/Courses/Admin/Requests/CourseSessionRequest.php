<?php

namespace App\OurEdu\Courses\Admin\Requests;

use Illuminate\Validation\Rule;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\Users\Repository\InstructorRepositoryInterface;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use Carbon\Carbon;

class CourseSessionRequest extends BaseAppRequest
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
        $rules =  [
            // sessions
            'content'    => 'required',
        ];
        if($this->required){
            $rules['date'] = "required|date|after_or_equal:today";
            $rules['start_time'] = ["required", "date_format:H:i:s",'before:end_time'];
            $rules['end_time'] = ["required", "date_format:H:i:s",'after:start_time'];
        }

        if(Carbon::parse($this->get('date'))->toDateString() == Carbon::today()->toDateString()){
            $rules['start_time'][] = 'after:'.Carbon::now()->addMinute(3)->format('H:i:s');
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $id = $this->route('id');
        if ($data = CourseSession::find($id)) {
            if (Carbon::parse($data->date . ' ' . $data->start_time)->lessThanOrEqualTo(Carbon::now())) {
                $this->required = false;
            }
        }


        if ( $this->start_time && $this->end_time) {

            $course = CourseSession::find( $this->route('id'))->course;

            $sessions = $this->instructotReposatory->getInstructorSessions($course->instructor_id);

            if ($id) {
                $sessions = $sessions->where('id', '!=', $id);
            }

            $sessions = $sessions->whereDate('date', $this->date);


            $from =  Carbon::parse($this->date .' '. $this->start_time)->format('H:I:s') ;
            $to=   Carbon::parse($this->date .' '.$this->end_time)->format('H:I:s');

                    if (
                    $sessions->whereBetween('start_time', [$from, $to])->count()
                    ) {
                        $this->addError("start_time", trans('validation.Session time intersects with another session in the same day'));
                        $this->addError("end_time", trans('validation.Session time intersects with another session in the same day'));
                    }

                    if (
                    $sessions->whereBetween('end_time', [$from, $to])->count()
                    ) {
                        $this->addError("start_time", trans('validation.Session time intersects with another session in the same day'));
                        $this->addError("end_time", trans('validation.Session time intersects with another session in the same day'));
                    }


            $schedules = VCRSchedule::query()->where('instructor_id', $this->instructor_id)
            ->whereDate('from_date','<=', $this->date)
            ->whereDate('to_date','>=', $this->date);

                if (
                    $schedules->wherehas('workingDays', function($dayes)  use ($from , $to){
                        $dayes->whereBetween('from_time', [$from, $to]);

                    })->count()
                    ) {
                        $this->addError("start_time", trans('validation.Session time intersects with another session in the same day'));
                        $this->addError("end_time", trans('validation.Session time intersects with another session in the same day'));
                    }

                    if (
                        $schedules->wherehas('workingDays', function($dayes)  use ($from , $to){
                            $dayes->whereBetween('to_time', [$from, $to]);

                        })->count()
                    ) {
                        $this->addError("start_time", trans('validation.Session time intersects with another session in the same day'));
                        $this->addError("end_time", trans('validation.Session time intersects with another session in the same day'));
                    }

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
