<?php

namespace App\OurEdu\Courses\Admin\Requests;

use App\OurEdu\Courses\Models\SubModels\CourseSession;
use Illuminate\Validation\Rule;
use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Users\Repository\InstructorRepositoryInterface;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use Carbon\Carbon;

class LiveSessionRequest extends BaseAppRequest
{
    private $instructotReposatory;
    /**
     * @var false
     */
    private bool $required = false;

    public function __construct(
        InstructorRepositoryInterface $instructotReposatory
    )
    {
        $this->instructotReposatory = $instructotReposatory;
    }

    public function rules()
    {
        $rules = [
            'name'  =>  'required',
            'subject_id'    =>  [
                'required',
                'integer',
                'exists:subjects,id'
            ],
            'subscription_cost' =>  'required|numeric',
            'instructor_id' =>  'required|integer|exists:users,id',
            'is_active' =>  'required|boolean',
            'content' => "required",
        ];

        if ($this->required) {
            $rules['date'] = "required|date|after_or_equal:today";
            $rules['start_time'] = ["required", "date_format:H:i:s",'before:end_time'];
            $rules['end_time'] = ["required", "date_format:H:i:s",'after:start_time'];
        }

        if (Carbon::parse($this->get('date'))->toDateString() == Carbon::today()->toDateString()) {
            $rules['start_time'][] = 'after:'.Carbon::now()->addMinute(3)->format('H:i:s');
        }

        if ($this->route('id')) {
            $rules["picture"] = ['image'];
        } else {
            $rules["picture"] = ['required', 'image'];
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

        if ($this->instructor_id && $this->date && $this->start_time && $this->end_time) {
            $sessions = $this->instructotReposatory->getInstructorSessions($this->instructor_id);

            if ($id) {
                $sessions = $sessions->where('course_id', '!=', $id);
            }

            $from = $this->start_time;
            $to=   $this->end_time;

            $sessions=  $sessions->where('date', $this->date);
            if ($sessions->whereBetween('start_time', [$from, $to])->count()
                ) {
                    $this->addError("start_time", trans('validation.Session time intersects with another session in the same day'));
                    $this->addError("end_time", trans('validation.Session time intersects with another session in the same day'));
            }

            if ($sessions->whereBetween('end_time', [$from, $to])->count()
                ) {
                $this->addError("start_time", trans('validation.Session time intersects with another session in the same day'));
                $this->addError("end_time", trans('validation.Session time intersects with another session in the same day'));
            }


                $schedules = VCRSchedule::query()->where('instructor_id', $this->instructor_id)
                ->whereDate('from_date', '<=', $this->date)
                ->whereDate('to_date', '>=', $this->date);

            if ($schedules->wherehas(
                'workingDays',
                function ($dayes) use ($from, $to) {
                        $dayes->whereBetween('from_time', [$from, $to]);
                }
            )->count()
                ) {
                    $this->addError("start_time", trans('validation.Session time intersects with another session in the same day'));
                    $this->addError("end_time", trans('validation.Session time intersects with another session in the same day'));
            }

            if ($schedules->wherehas(
                'workingDays',
                function ($dayes) use ($from, $to) {
                            $dayes->whereBetween('to_time', [$from, $to]);
                }
            )->count()
                ) {
                $this->addError("start_time", trans('validation.Session time intersects with another session in the same day'));
                $this->addError("end_time", trans('validation.Session time intersects with another session in the same day'));
            }
        }
    }

    protected function addError($key, $message)
    {
        $validator = $this->getValidatorInstance();

        $validator->after(
            function ($validator) use ($key, $message) {
                $validator->errors()->add($key, $message);
            }
        );
    }
}
