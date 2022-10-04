<?php

namespace App\OurEdu\GeneralExams\UseCases\GeneralExam;

use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepositoryInterface;
use App\OurEdu\GeneralExams\Repository\Question\GeneralExamQuestionRepositoryInterface;

class GeneralExamUseCase implements GeneralExamUseCaseInterface
{
    protected $exam;
    protected $typeMethods;
    protected $questionRepository;
    protected $examRepository;

    public function __construct(GeneralExamQuestionRepositoryInterface $questionRepository, GeneralExamRepositoryInterface $examRepository)
    {
        $this->typeMethods = [
                LearningResourcesEnums::MULTI_CHOICE => 'multipleChoiceGenerate',
                LearningResourcesEnums::TRUE_FALSE => 'trueFalseGenerate',
                LearningResourcesEnums::DRAG_DROP => 'dragDropGenerate',
                LearningResourcesEnums::MATCHING => 'matchingGenerate',
                LearningResourcesEnums::MULTIPLE_MATCHING => 'multipleMatchingGenerate',
                LearningResourcesEnums::COMPLETE => 'completeGenerate',
                LearningResourcesEnums::HOTSPOT => 'hotspotGenerate',
        ];

        $this->questionRepository = $questionRepository;
        $this->examRepository = $examRepository;
    }

    public function create($data)
    {
        $dataArray = array_merge($data->toArray(), [
            'subject_format_subjects' => json_encode($data->subject_format_subjects),
            'uuid'  =>  Str::uuid()
        ]);

        return $this->examRepository->create($dataArray);
    }


    public function updateQuestions($exam, $data)
    {
        if ($exam->published_at) {
            throw new ErrorResponseException(trans('api.You cant update published exam'));
        }

        $exam->preparedQuestions()->sync($data->prepared_questions);
    }

    public function update($exam, $data)
    {
        if ($exam->published_at) {
            throw new ErrorResponseException(trans('api.You cant update published exam'));
        }

        $dataArray = array_merge($data->toArray(), ['subject_format_subjects' => json_encode($data->subject_format_subjects)]);

        $this->examRepository->update($exam, $dataArray);
    }

    public function delete($exam)
    {
        // not started or not published
        if (now()->lte(Carbon::parse($exam->date . ' ' . $exam->start_time)) || ! $exam->published_at) {
            return $this->examRepository->delete($exam);
        }

        throw new ErrorResponseException(trans('api.You cant delete exam after start time'));
    }


    public function publishGeneralExam($exam)
    {
        $this->exam = $exam;

        // no prepared questions
        if (! $exam->preparedQuestions()->count()) {
            throw new ErrorResponseException(trans('api.General exam doesnt have questions to be published'));
        }

        // trying to publish exam after start time
        if (! now()->lte(Carbon::parse($exam->date . ' ' . $exam->start_time))) {
            throw new ErrorResponseException(trans('api.You cant publish exam after start time'));
        }

        // trying to publish exam a published exam
        if ($exam->published_at) {
            throw new ErrorResponseException(trans('api.General exam already published'));
        }

        $this->createQuestions();
        $this->detachPreparedQuestions();
        $this->markAsPublished();
    }

    protected function createQuestions()
    {
        $this->exam->preparedQuestions->each(function ($question) {
            if (method_exists($this, $method = $this->typeMethods[$question->question_type])) {
                $this->$method($question);
            }
        });
    }

    protected function detachPreparedQuestions()
    {
        return $this->exam->preparedQuestions()->detach();
    }

    protected function markAsPublished()
    {
        $this->examRepository->markAsPublished($this->exam);
    }


    protected function trueFalseGenerate($preparedQuestion)
    {
        $questionData = [
            'general_exam_id'   =>  $this->exam->id,
            'question_type' =>  $preparedQuestion->questionable->parentData->TrueFalseType->slug ?? $preparedQuestion->question_type,
            'description' => $preparedQuestion->questionable->parentData->description,
            'question'  =>  $preparedQuestion->questionable->text ?? '',
            'is_true'   =>  $preparedQuestion->questionable->is_true ?? null,
            'subject_format_subject_id' =>  $preparedQuestion->subject_format_subject_id,
            'difficulty_level_id' =>  $preparedQuestion->difficulty_level_id,
            'questionable_type' => $preparedQuestion->questionable_type ,
            'questionable_id' => $preparedQuestion->questionable_id,
        ];

        $question = $this->questionRepository->create($questionData);


        if ($preparedQuestion->questionable->options) {
            $options = collect([]);

            $preparedQuestion->questionable->options->each(function ($option) use ($options) {
                $options->push([
                    'option'    =>  $option->option,
                    'is_correct'    =>  $option->is_correct_answer
                ]);
            });

            $options = $this->questionRepository->createOptions($question, $options->toArray());

            // create question media
            if ($preparedQuestion->questionable->media) {
                $this->questionRepository->createMedia($question, $preparedQuestion);
            }
        }
    }

    protected function multipleChoiceGenerate($preparedQuestion)
    {
        $questionData = [
            'general_exam_id'   =>  $this->exam->id,
            'question_type' =>  $preparedQuestion->questionable->parentData->multipleChoiceType->slug ?? $preparedQuestion->question_type,
            'description' => $preparedQuestion->questionable->parentData->description,
            'question'  =>  $preparedQuestion->questionable->question ?? '',
            'subject_format_subject_id' =>  $preparedQuestion->subject_format_subject_id,
            'difficulty_level_id' =>  $preparedQuestion->difficulty_level_id,
            'questionable_type' => $preparedQuestion->questionable_type ,
            'questionable_id' => $preparedQuestion->questionable_id,
        ];

        $question = $this->questionRepository->create($questionData);

        $options = collect([]);

        $preparedQuestion->questionable->options->each(function ($option) use ($options) {
            $options->push([
                'option'    =>  $option->answer,
                'is_correct'    =>  $option->is_correct_answer
            ]);
        });

        $options = $this->questionRepository->createOptions($question, $options->toArray());

        // create question media
        if ($preparedQuestion->questionable->media) {
            $this->questionRepository->createMedia($question, $preparedQuestion);
        }
    }

    protected function completeGenerate($preparedQuestion)
    {
        $questionData = [
            'general_exam_id'   =>  $this->exam->id,
            'question_type' =>  $preparedQuestion->question_type,
            'question'  =>  $preparedQuestion->questionable->question ?? '',
            'description' => $preparedQuestion->questionable->parentData->description,
            'subject_format_subject_id' =>  $preparedQuestion->subject_format_subject_id,
            'difficulty_level_id' =>  $preparedQuestion->difficulty_level_id,
            'questionable_type' => $preparedQuestion->questionable_type ,
            'questionable_id' => $preparedQuestion->questionable_id,
        ];

        $question = $this->questionRepository->create($questionData);

        $options = collect([]);

        $options->push([
            'option'    =>  $preparedQuestion->questionable->answer->answer ?? '',
            'is_correct'    =>  true,
            'is_main_answer'   =>  true
        ]);

        if ($preparedQuestion->questionable->acceptedAnswers) {
            $preparedQuestion->questionable->acceptedAnswers->each(function ($option) use ($options) {
                $options->push([
                    'option'    =>  $option->answer,
                    'is_correct'    =>  true,
                    'is_main_answer'   =>  false
                ]);
            });
        }

        $options = $this->questionRepository->createOptions($question, $options->toArray());

        // create question media
        if ($preparedQuestion->questionable->media) {
            $this->questionRepository->createMedia($question, $preparedQuestion);
        }
    }

    protected function hotspotGenerate($preparedQuestion)
    {
        $questionData = [
            'general_exam_id'   =>  $this->exam->id,
            'question_type' =>  $preparedQuestion->question_type,
            'question'  =>  $preparedQuestion->questionable->question ?? '',
            'description' => $preparedQuestion->questionable->parentData->description,
            'subject_format_subject_id' =>  $preparedQuestion->subject_format_subject_id,
            'difficulty_level_id' =>  $preparedQuestion->difficulty_level_id,
            'questionable_type' => $preparedQuestion->questionable_type ,
            'questionable_id' => $preparedQuestion->questionable_id,
        ];

        $question = $this->questionRepository->create($questionData);

        $options = collect([]);

        $options->push([
            'option'    =>  $preparedQuestion->questionable->answer->answer ?? '',
            'is_correct'    =>  true,
            'is_main_answer'   =>  true
        ]);

        $options = $this->questionRepository->createOptions($question, $options->toArray());

        // create question media
        if ($preparedQuestion->questionable->media) {
            $this->questionRepository->createMedia($question, $preparedQuestion);
        }
    }

    protected function dragDropGenerate($preparedQuestion) {
        $questionData = [
            'general_exam_id'   =>  $this->exam->id,
            'question_type' =>  $preparedQuestion->question_type,
            'question'  =>  $preparedQuestion->questionable->description ?? '',
            'description'  =>  $preparedQuestion->questionable->description ?? '',
            'subject_format_subject_id' =>  $preparedQuestion->subject_format_subject_id,
            'difficulty_level_id' =>  $preparedQuestion->difficulty_level_id,
            'questionable_type' => $preparedQuestion->questionable_type ,
            'questionable_id' => $preparedQuestion->questionable_id,
        ];

        $data = $this->questionRepository->create($questionData);

        //Create Options

        //Create Questions (correct option id)
        $optionsIdsPair = [];
        foreach ($preparedQuestion->questionable->options as $option) {
            $op = $data->options()->create([
                'option' => $option->option
            ]);
            $optionsIdsPair[$option->id] = $op->id;
        }

        foreach ($preparedQuestion->questionable->questions as $question) {
            $q = $data->questions()->create([
                    'question' => $question->question,
                    'general_exam_correct_option_id' => $optionsIdsPair[$question->correct_option_id]
                ]);

            // create question media
            if ($question->media) {
                $this->questionRepository->createMedia($q, $question);
            }
        }

    }
    protected function matchingGenerate($preparedQuestion) {
        $questionData = [
            'general_exam_id'   =>  $this->exam->id,
            'question_type' =>  $preparedQuestion->question_type,
            'question'  =>  $preparedQuestion->questionable->description ?? '',
            'description'  =>  $preparedQuestion->questionable->description ?? '',
            'subject_format_subject_id' =>  $preparedQuestion->subject_format_subject_id,
            'difficulty_level_id' =>  $preparedQuestion->difficulty_level_id,
            'questionable_type' => $preparedQuestion->questionable_type ,
            'questionable_id' => $preparedQuestion->questionable_id,
        ];

        $data = $this->questionRepository->create($questionData);

        //Create Questions
        foreach ($preparedQuestion->questionable->questions as $question) {
            $q = $data->questions()->create([
                'question' => $question->text
            ]);

            foreach ($question->options as $option) {
                $q->options()->create([
                    'option' => $option->option,
                    'general_exam_question_id' => $data->id
                ]);
            }

            // create question media
            if ($question->media) {
                $this->questionRepository->createMedia($q, $question);
            }
        }
    }
    protected function multipleMatchingGenerate($preparedQuestion) {

        $questionData = [
            'general_exam_id'   =>  $this->exam->id,
            'question_type' =>  $preparedQuestion->question_type,
            'question'  =>  $preparedQuestion->questionable->description ?? '',
            'description'  =>  $preparedQuestion->questionable->description ?? '',
            'subject_format_subject_id' =>  $preparedQuestion->subject_format_subject_id,
            'difficulty_level_id' =>  $preparedQuestion->difficulty_level_id,
            'questionable_type' => $preparedQuestion->questionable_type ,
            'questionable_id' => $preparedQuestion->questionable_id,
        ];

        $data = $this->questionRepository->create($questionData);

        //Create Questions
        $oldOptionsIds = [];
        foreach ($preparedQuestion->questionable->options as $option) {
            $op = $data->options()->create([
                'option' => $option->option
            ]);
            $oldOptionsIds[$option->id] = $op->id;
        }

        foreach ($preparedQuestion->questionable->questions as $question) {
            $q = $data->questions()->create([
                'question' => $question->text
            ]);

            $options = $question->options()->pluck('res_multi_matching_option_id')->toArray();

            foreach ($options as $option) {
                $q->multiMatchingOptions()->sync([
                    $oldOptionsIds[$option] => ['general_exam_question_id' => $data->id]
                ]);
            }

            // create question media
            if ($question->media) {
                $this->questionRepository->createMedia($q, $question);
            }
        }
    }

    public function generalExamStudent($examId)
    {
        $exam = $this->examRepository->findOrFail($examId);
        $examRepo = new GeneralExamRepository($exam);
        return $examRepo->generalExamStudents();
    }
}
