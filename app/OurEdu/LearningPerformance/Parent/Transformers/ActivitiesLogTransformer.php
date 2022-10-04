<?php


namespace App\OurEdu\LearningPerformance\Parent\Transformers;

use App\OurEdu\Events\Enums\StudentEventsEnum;
use App\OurEdu\Events\Models\StudentStoredEvent;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class ActivitiesLogTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];


    public function transform(StudentStoredEvent $event)
    {
        $transformedData = [
            'id' => Str::uuid(),
            'date' => (string) $event->created_at->diffForHumans(),
        ];

        // case if the event is exam related
        if (isset($event->event_properties['exam_attributes'])) {
            $transformedData['text'] = (string) trans(
                "events.{$event->event_properties['action']}",
                [
                'exam_title' => $event->event_properties['exam_attributes']['exam_title']
                ]
            );

            $transformedData['exam_title'] = (string) trans(
                'app.exam_on',
                [
                'title' => $event->event_properties['exam_attributes']['exam_title']
                ]
            );
            $transformedData['difficulty_level'] = (string) trans("difficulty_levels.{$event->event_properties['exam_attributes']['difficulty_level']}");
            $transformedData['questions_number'] = (string) $event->event_properties['exam_attributes']['questions_number'];

            if ($event->event_properties['action'] == StudentEventsEnum::STUDENT_FINISHED_EXAM) {
                $transformedData['result'] = (int) $event->event_properties['exam_attributes']['result'];
            }
        }


        // case if the event is practice related
        if (isset($event->event_properties['practice_attributes'])) {
            $transformedData['text'] = (string) trans(
                "events.{$event->event_properties['action']}",
                [
                'exam_title' => $event->event_properties['practice_attributes']['exam_title']
                ]
            );

            $transformedData['exam_title'] = (string) trans(
                'app.practice_on',
                [
                'title' => $event->event_properties['practice_attributes']['exam_title']
                ]
            );
            $transformedData['difficulty_level'] = (string) trans("difficulty_levels.{$event->event_properties['practice_attributes']['difficulty_level']}");
        }


        // case if the event is competition related
        if (isset($event->event_properties['competition_attributes'])) {
            $transformedData['text'] = (string) trans(
                "events.{$event->event_properties['action']}",
                [
                'exam_title' => $event->event_properties['competition_attributes']['exam_title']
                ]
            );

            $transformedData['exam_title'] = (string) trans(
                'app.competition_on',
                [
                'title' => $event->event_properties['competition_attributes']['exam_title']
                ]
            );
            $transformedData['difficulty_level'] = (string) trans("difficulty_levels.{$event->event_properties['competition_attributes']['difficulty_level']}");
            $transformedData['questions_number'] = (string) $event->event_properties['competition_attributes']['questions_number'];

            if ($event->event_properties['action'] == StudentEventsEnum::STUDENT_FINISHED_COMPETITION) {
                $transformedData['result'] = (int) $event->event_properties['competition_attributes']['result'];
            }
        }

        return $transformedData;
    }
}
