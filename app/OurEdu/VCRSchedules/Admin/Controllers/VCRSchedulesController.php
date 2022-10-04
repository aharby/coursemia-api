<?php

namespace App\OurEdu\VCRSchedules\Admin\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\VCRSchedules\Admin\Requests\VCRScheduleRequest;
use App\OurEdu\VCRSchedules\Repository\VCRScheduleRepositoryInterface;

class VCRSchedulesController extends BaseController
{
    private $module;
    private $vcrScheduleRepository;
    private $userRepository;
    private $subjectRepository;
    private $title;
    private $parent;


    public function __construct(
        VCRScheduleRepositoryInterface $vcrScheduleRepository,
        SubjectRepositoryInterface $subjectRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->module = 'vcrSchedules';
        $this->title = trans('vcr_schedule.VCRSchedules');
        $this->parent = ParentEnum::ADMIN;
        $this->vcrScheduleRepository = $vcrScheduleRepository;
        $this->subjectRepository = $subjectRepository;
        $this->userRepository = $userRepository;
    }

    public function getIndex()
    {
        $data['rows'] = $this->vcrScheduleRepository->paginate();
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {
        $data['page_title'] = trans('app.Create') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.vcr_schedules.get.index')];
        $data = array_merge($data, $this->lookup());
        return view($this->parent . '.' . $this->module . '.create', $data);
    }


    public function postCreate(VCRScheduleRequest $request)
    {
        try {
            $data = [
                'subject_id' =>$request->input('subject_id'),
                'instructor_id' =>$request->input('instructor_id'),
                'from_date' =>$request->input('from_date'),
                'to_date' =>$request->input('to_date'),
                'is_active' =>$request->input('is_active'),
                'price' =>$request->input('price'),
            ];
            DB::beginTransaction();
            $vcrSchedule = $this->vcrScheduleRepository->create($data);
            foreach (request()->input('working_days') as $day => $times) {
                if (isset($times['day'])) {
                    $this->vcrScheduleRepository->createWorkingDays([
                        'vcr_schedule_instructor_id' => $vcrSchedule->id,
                        'day' => $day,
                        'from_time' =>  $times['from_time'] ? Carbon::parse($times['from_time'])->format('H:i:s') : null,
                        'to_time' =>  $times['to_time'] ? Carbon::parse($times['to_time'])->format('H:i:s') :null,
                    ]);
                }
            }
            DB::commit();
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.vcr_schedules.get.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            flash()->error(trans('app.Oopps Something is broken'));
            return redirect()->back();
        }
    }

    public function getEdit($id)
    {
        $data['row'] = $this->vcrScheduleRepository->findOrFail($id);
        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.subjects.get.index')];
        $data = array_merge($data, $this->lookup());
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    public function putEdit(VCRScheduleRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $vcrSchedule = $this->vcrScheduleRepository->findOrFail($id);
            $data = [
                'subject_id' =>$request->input('subject_id'),
                'instructor_id' =>$request->input('instructor_id'),
                'from_date' =>$request->input('from_date'),
                'to_date' =>$request->input('to_date'),
                'is_active' =>$request->input('is_active'),
                'price' =>$request->input('price'),
            ];
            $this->vcrScheduleRepository->update($vcrSchedule, $data);
            foreach (\App\OurEdu\VCRSchedules\DayEnums::weekDays() as $weekDay) {
                $workingDay = $this->vcrScheduleRepository->getWorkingDay($vcrSchedule->id, $weekDay);
                if (isset(request()->input('working_days')[$weekDay]['day'])) {
                    $dayTimes = request()->input('working_days')[$weekDay];
                    if ($workingDay) {
                        $this->vcrScheduleRepository->updateWorkingDays($workingDay, [
                            'vcr_schedule_instructor_id' => $vcrSchedule->id,
                            'day' => $weekDay,
                            'from_time' => $dayTimes['from_time'] ? Carbon::parse($dayTimes['from_time'])->format('H:i:s') : null,
                            'to_time' => $dayTimes['to_time'] ? Carbon::parse($dayTimes['to_time'])->format('H:i:s') : null,
                        ]);
                    } else {
                        $this->vcrScheduleRepository->createWorkingDays([
                            'vcr_schedule_instructor_id' => $vcrSchedule->id,
                            'day' => $weekDay,
                            'from_time' =>  $dayTimes['from_time'] ? Carbon::parse($dayTimes['from_time'])->format('H:i:s') : null,
                            'to_time' =>  $dayTimes['to_time'] ? Carbon::parse($dayTimes['to_time'])->format('H:i:s') :null,
                        ]);
                    }
                } else {
                    if ($workingDay) {
                        $workingDay->delete();
                    }
                }
            }
            DB::commit();
            flash()->success(trans('app.Updated successfully'));
            return redirect()->route('admin.vcr_schedules.get.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e->getMessage());
            Log::error($e);
            flash()->error(trans('app.Oopps Something is broken'));
            return redirect()->back();
        }
    }

    public function getView($id)
    {
        $data['row'] = $this->vcrScheduleRepository->findOrFail($id);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.vcr_schedules.get.index')];
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function delete($id)
    {
        $row = $this->vcrScheduleRepository->findOrFail($id);
        if ($this->vcrScheduleRepository->delete($row)) {
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.vcr_schedules.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function lookup()
    {
        $data['instructors'] = $this->userRepository->getPluckInstructors();
        $data['subjects'] = $this->subjectRepository->getPluckSubjectsToArray();
        return $data;
    }

    public function getWorkingDayes($from , $to)
    {
      $dayes = $this->vcrScheduleRepository->getWorkingDayes($from , $to);

      return response()->json([
          'status' => 200,
           'dayes' => $dayes
      ]);

    }
}
