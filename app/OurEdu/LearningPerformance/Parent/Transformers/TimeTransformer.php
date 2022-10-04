<?php

namespace App\OurEdu\LearningPerformance\Parent\Transformers;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use League\Fractal\TransformerAbstract;

class TimeTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [];

    public function transform($time)
    {
        $timable = $time->timable;
        $timableName = $this->resolveTimableName($timable , $time->timable_type);
        $transformedData = [
            'id' => $time->id,
            'title' => $timableName ,
            'time' => number_format($time->time / 60 , 2) . " " . trans('subject.Minutes'),
            'start_time'=> $time->start_time
       ];
        return $transformedData;
    }

    private function resolveTimableName($timable , $timableType){
        $name = "";

        switch ($timableType) {
            case SubjectFormatSubject::class:
                $name =  $timable->title;
                break;
            case ResourceSubjectFormatSubject::class:
                $data = getResourceData($timable);
                $name =  $data->title;
                break;
        }

        return  $name;
    }
}
