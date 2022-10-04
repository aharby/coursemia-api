<?php

namespace App\OurEdu\VCRSchedules\Admin\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\VCRSchedules\Repository\VCRScheduleRepositoryInterface;
use App\OurEdu\Users\Repository\InstructorRepositoryInterface;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use Carbon\Carbon;

class VCRScheduleRequest extends BaseAppRequest
{
    private $vcrScheduleRepository;
    private $instructotReposatory;

    public function __construct(
        VCRScheduleRepositoryInterface $vcrScheduleRepository,
        InstructorRepositoryInterface $instructotReposatory 
    ) {

        $this->vcrScheduleRepository = $vcrScheduleRepository;
        $this->instructotReposatory = $instructotReposatory;
    }
    public function rules()
    {
        $daysRules = [];

        foreach (\App\OurEdu\VCRSchedules\DayEnums::weekDays() as $weekDay) {
            $daysRules['working_days.'.$weekDay.'.day'] = 'nullable';
            $daysRules['working_days.'.$weekDay.'.from_time'] = 'required_with:working_days.'.$weekDay.'.day';
            $daysRules['working_days.'.$weekDay.'.to_time'] = 'required_with:working_days.'.$weekDay.'.day';
        }

        $rules = [
            'subject_id' => 'required',
            'instructor_id' => 'required',
            'is_active' => 'required|boolean',
            'from_date' => 'required|date_format:"Y-m-d"|before:to_date',
            'to_date' => 'required|date_format:"Y-m-d"|after:from_date',
            'price' => 'required|numeric|min:0',
        ];

        return array_merge($rules, $daysRules);
    }

    protected function prepareForValidation()
    {

        if ($this->instructor_id && $this->from_date && $this->to_date)
        {

            foreach($this->working_days as $day => $times){

                if (isset($times['day']) and  isset($times['from_time']) and isset($times['to_time'])) {

                    $fromDate = new \DateTime($this->from_date);
                    if(strtolower($fromDate->format('l')) == strtolower($times['day'])){
                        $date = $fromDate->format('Y-m-d');
                    }else{
                    $date = $fromDate->modify("next ".$times['day'])->format('Y-m-d');
                    }

                    $from =  Carbon::parse($times['from_time'])->format('H:I:s') ;
                    $to=   Carbon::parse($times['to_time'])->format('H:I:s');

                    $sessions = $this->instructotReposatory->getInstructorSessions($this->instructor_id);

                    $sessions = $sessions->whereDate('date' , $date);


                    if (
                        $sessions->whereBetween('start_time', [$from, $to])->count()
                        ) {
                            $this->addError('working_days.'.$day .'.from_time', trans('validation.Session time intersects with another session in the same day'));
                            $this->addError("working_days.".$day .".end_time", trans('validation.Session time intersects with another session in the same day'));
            }

                        if (
                        $sessions->whereBetween('end_time', [$from, $to])->count()
                        ) {

                            $this->addError('working_days.'.$day .'.from_time', trans('validation.Session time intersects with another session in the same day'));
                            $this->addError("working_days.".$day .".end_time", trans('validation.Session time intersects with another session in the same day'));
           }


                $schedules = VCRSchedule::query()->where('instructor_id', $this->instructor_id)
                ->whereDate('from_date','<=', $date)
                ->whereDate('to_date','>=', $date);


                if ($id = $this->route('id')) {
                    $schedules = $schedules->where('id', '!=', $id);

                }

                    if (
                        $schedules->wherehas('workingDays', function($dayes)  use ($from , $to){
                            $dayes->whereBetween('from_time', [$from, $to]);

                        })->count()
                        ) {
                            $this->addError('working_days.'.$day .'.from_time', trans('validation.Session time intersects with another session in the same day'));
                            $this->addError("working_days.".$day .".end_time", trans('validation.Session time intersects with another session in the same day'));
               }

                        if (
                            $schedules->wherehas('workingDays', function($dayes)  use ($from , $to){
                                $dayes->whereBetween('to_time', [$from, $to]);

                            })->count()
                        ) {

                            $this->addError('working_days.'.$day .'.from_time', trans('validation.Session time intersects with another session in the same day'));
                            $this->addError("working_days.".$day .".end_time", trans('validation.Session time intersects with another session in the same day'));
                }

                }
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

    public function messages()
    {
        $array = [];

        foreach (\App\OurEdu\VCRSchedules\DayEnums::weekDays() as $weekDay) {
            $array['working_days.'.$weekDay.'.from_time.required_with']= trans('vcr_schedule.Please choose day shift to time');
            $array['working_days.'.$weekDay.'.to_time.required_with'] = trans('vcr_schedule.Please choose day shift to time');
        }

        return $array;
    }




}
