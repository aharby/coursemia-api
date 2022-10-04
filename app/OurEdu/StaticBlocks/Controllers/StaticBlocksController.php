<?php

namespace App\OurEdu\StaticBlocks\Controllers;



use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\StaticBlocks\Repository\StaticBlocksRepositoryInterface;

class StaticBlocksController extends BaseController
{
    private $module;
    private $staticBlocksRepository;
    private $title;
    private $parent;


    public function __construct(StaticBlocksRepositoryInterface $staticBlocksRepository
    )
    {
        $this->module = 'staticBlocks';
        $this->title = trans('static_blocks.Static Blocks');
        $this->parent = ParentEnum::ADMIN;
        $this->staticBlocksRepository = $staticBlocksRepository;
    }

    public function getIndex()
    {
   /*     $data['rows'] = $this->vcrScheduleRepository->paginate();
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);*/
    }

    public function getEdit($id)
    {/*
        $data['row'] = $this->vcrScheduleRepository->findOrFail($id);
        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.subjects.get.index')];
        $data = array_merge($data, $this->lookup());
        return view($this->parent . '.' . $this->module . '.edit', $data);
   */ }

    public function putEdit(StaticBlocksRequest $request, $id)
    {/*
        try {
            DB::beginTransaction();
            $vcrSchedule = $this->vcrScheduleRepository->findOrFail($id);
            $data = [
                'subject_id' =>$request->input('subject_id'),
                'instructor_id' =>$request->input('instructor_id'),
                'from_date' =>$request->input('from_date'),
                'to_date' =>$request->input('to_date'),
                'is_active' =>$request->input('is_active'),
            ];
            $this->vcrScheduleRepository->update($vcrSchedule, $data);
            foreach (request()->input('working_days') as $day => $times) {
                $workingDay = $this->vcrScheduleRepository->getWorkingDay($vcrSchedule->id, $day);
                $this->vcrScheduleRepository->updateWorkingDays($workingDay, [
                    'vcr_schedule_instructor_id' => $vcrSchedule->id,
                    'day' => $day,
                    'from_time' => $times['from'] ? Carbon::parse($times['from'])->format('H:i:s') : null,
                    'to_time' => $times['to'] ? Carbon::parse($times['to'])->format('H:i:s') : null,
                ]);
            }
            DB::commit();
            flash()->success(trans('app.Updated successfully'));
            return redirect()->route('admin.vcrSchedules.get.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            flash()->error(trans('app.Oopps Something is broken'));
            return redirect()->back();
        }*/
    }

    public function delete($id)
    {
      /*  $row = $this->vcrScheduleRepository->findOrFail($id);
        if ($this->vcrScheduleRepository->delete($row)) {
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.vcrSchedules.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();*/
    }

}

