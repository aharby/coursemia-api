<?php


namespace App\OurEdu\GeneralExams\UseCases\AnswerUseCase\PostAnswerUseCase;

use Swis\JsonApi\Client\Collection;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepositoryInterface;
use App\OurEdu\GeneralExams\Repository\Question\GeneralExamQuestionRepositoryInterface;
use App\OurEdu\GeneralExams\Repository\GeneralExamStudent\GeneralExamStudentRepositoryInterface;

class PostAnswerUseCase implements PostAnswerUseCaseInterface
{
    protected $user;
    protected $examRepository;
    protected $examQuestionRepository;
    protected $generalExamStudentRepository;
    protected $answerMethods = [];

    public function __construct(
        GeneralExamRepositoryInterface $examRepository,
        GeneralExamStudentRepositoryInterface $generalExamStudentRepository,
        GeneralExamQuestionRepositoryInterface $examQuestionRepository
    ) {
        $this->examRepository = $examRepository;
        $this->generalExamStudentRepository = $generalExamStudentRepository;
        $this->examQuestionRepository = $examQuestionRepository;

        $this->user = Auth::guard('api')->user();

        $this->answerMethods = [
                ResourceOptionsSlugEnum::MULTIPLE_CHOICE_SLUG_SINGLE_CHOICE => 'singleChoiceAnswer',
                ResourceOptionsSlugEnum::MULTIPLE_CHOICE_SLUG_MULTIPLE_CHOICE => 'multipleChoiceAnswer',
                ResourceOptionsSlugEnum::TRUE_FALSE => 'trueFalseAnswer',
                ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT => 'trueFalseWithCorrectAnswer',
                LearningResourcesEnums::DRAG_DROP => 'dragDropAnswer',
                LearningResourcesEnums::MATCHING => 'matchingAnswer',
                LearningResourcesEnums::MULTIPLE_MATCHING => 'multipleMatchingAnswer',
                LearningResourcesEnums::COMPLETE => 'completeAnswer',
                LearningResourcesEnums::HOTSPOT => 'hotspotAnswer',
        ];
    }

    /**
     * @param int $exam
     * @param Collection $data
     * @return array|void
     */
//    public function postAnswer(int $exam, int $questionId, array $answers)

    public function postAnswer(int $examId, Collection $data)
    {
        $exam = $this->examRepository->findOrFail($examId);

        $studentExam = $this->generalExamStudentRepository->findStudentExam($examId, $this->user->student->id);

        if (! $studentExam) {
            throw new ErrorResponseException(trans("api.Exam not started yet"));
        }

        if ($studentExam->is_finished) {
            throw new ErrorResponseException(trans("api.The exam is already finished"));
        }

        $data->each(function ($questionAnswerData) use ($exam) {
            if ($questionId = $questionAnswerData->getId()) {
                $question = $this->examQuestionRepository->findExamQuestion($exam, $questionId);


                if (method_exists($this, $method = $this->answerMethods[$question->question_type])) {
                    $examAnswerData = $this->$method($question, $questionAnswerData);
                    $this->updateOrCreateAnswer($question, $examAnswerData);
                }
            }
        });

        return $exam;
    }

    protected function updateOrCreateAnswer($question, $examAnswerData)
    {
        $this->examQuestionRepository->updateOrCreateAnswer($question, $examAnswerData);
    }


    protected function completeAnswer($question, $questionAnswerData)
    {
        if ($answerText = $questionAnswerData->answers->first()->answer_text) {
            $option = $question->options()->where('option', $answerText)->first();

            return [
                'student_id'    =>  $this->user->student->id,
                'general_exam_id'    =>  $question->general_exam_id,
                'general_exam_question_id'    =>  $question->id,
                'general_exam_option_id'    =>  $option->id ?? null,
                'answer_text'    =>  $answerText,
                'is_correct'    =>  $option ? true : false,
            ];
        }

        throw new ErrorResponseException("api.Unable to define answer");
    }

    protected function singleChoiceAnswer($question, $questionAnswerData)
    {
        if ($answer = $questionAnswerData->answers->first()) {
            $option = $question->options->where('id', $answer->answer_id)->first();

            return [
                'student_id'    =>  $this->user->student->id,
                'general_exam_id'    =>  $question->general_exam_id,
                'general_exam_question_id'    =>  $question->id,
                'general_exam_option_id'    =>  $option->id ?? null,
                'answer_text'    =>  null,
                'is_correct'    =>  $option->is_correct ?? false,
            ];
        }

        throw new ErrorResponseException("api.Unable to define answer");
    }

    protected function multipleChoiceAnswer($question, $questionAnswerData)
    {
        if ($answers = $questionAnswerData->answers) {
            $answerIds = collect($answers)->pluck('answer_id');

            $answerCorrectoptions = $question->options->whereIn('id', $answerIds)->where('is_correct', true);
            $questionCorrectOptions = $question->options->where('is_correct', true);

            $isCorrect = (bool) ($answerCorrectoptions->count() == $questionCorrectOptions->count());

            return [
                'student_id'    =>  $this->user->student->id,
                'general_exam_id'    =>  $question->general_exam_id,
                'general_exam_question_id'    =>  $question->id,
                'general_exam_option_id'    =>  $answerIds->first(),
                'answer_text'    =>  null,
                'is_correct'    =>  $isCorrect,
            ];
        }

        throw new ErrorResponseException("api.Unable to define answer");
    }

    protected function trueFalseAnswer($question, $questionAnswerData)
    {
        if ($answer = $questionAnswerData->answers->first()) {
            $isCorrect = $question->is_true == $answer->answer_text ?? false;

            return [
                'student_id'    =>  $this->user->student->id,
                'general_exam_id'    =>  $question->general_exam_id,
                'general_exam_question_id'    =>  $question->id,
                'general_exam_option_id'    =>  null,
                'answer_text'    =>  $answer->answer_text,
                'is_correct'    =>  $isCorrect,
            ];
        }

        throw new ErrorResponseException("api.Unable to define answer");
    }

    protected function trueFalseWithCorrectAnswer($question, $questionAnswerData)
    {
        $answer = $questionAnswerData->answers->first();

        if ($answer && $answer->answer_text === true) {  
                $isCorrect = $question->is_true == $answer->answer_text ?? false;

                return [
                    'student_id'    =>  $this->user->student->id,
                    'general_exam_id'    =>  $question->general_exam_id,
                    'general_exam_question_id'    =>  $question->id,
                    'general_exam_option_id'    =>  null,
                    'answer_text'    =>  $answer->answer_text,
                    'is_correct'    =>  $isCorrect,
                ];
        }

        if ($answer && is_numeric($answer->answer_id)) {
            $option = $question->options->where('id', $answer->answer_id)->first();

            $isCorrect = (bool) (! $answer->answer_text && $option->is_correct);

            return [
                    'student_id'    =>  $this->user->student->id,
                    'general_exam_id'    =>  $question->general_exam_id,
                    'general_exam_question_id'    =>  $question->id,
                    'general_exam_option_id'    =>  $option->id,
                    'answer_text'    =>  $answer->answer_text,
                    'is_correct'    =>  $isCorrect,
                ];
        }

        throw new ErrorResponseException("api.Unable to define answer");
    }

    protected function dragDropAnswer($question, $questionAnswerData)
    {
        $answersData =[];
        $isCorrectQuestionArray = [];
        $isCorrectQuestion = false;
        foreach ($questionAnswerData->answers as $answer) {
            $singleQuestionId = $answer->single_question_id;
            $isCorrect = $question->questions()->where('id', $singleQuestionId)->where('general_exam_correct_option_id', $answer->answer_id)->exists();
            $isCorrectQuestionArray[] = $isCorrect;
            $answersData[] = [
                'general_exam_option_id' => $answer->answer_id,
                'is_correct_answer' => $isCorrect,
                'question_id' => $question->id,
                'student_id'    =>  $this->user->student->id,
                'single_question_id' => $answer->single_question_id
            ];
        }

        if (in_array(false, $isCorrectQuestionArray, true) or ($question->questions()->count() != count($isCorrectQuestionArray)) ) {
            $isCorrectQuestion = false;
        } elseif (in_array(true, $isCorrectQuestionArray, true)) {
            $isCorrectQuestion = true;
        }

        $questionAnswer =  [
            'student_id'    =>  $this->user->student->id,
            'general_exam_id'    =>  $question->general_exam_id,
            'general_exam_question_id'    =>  $question->id,
            'general_exam_option_id'    =>  null,
            'answer_text'    =>  null,
            'is_correct'    =>  $isCorrectQuestion,
            'details' => $answersData
        ];

        return $questionAnswer;
        throw new ErrorResponseException("api.Unable to define answer");
    }


    protected function matchingAnswer($question, $questionAnswerData)
    {
        $answersData =[];
        $isCorrectQuestionArray = [];
        $isCorrectQuestion = false;
        foreach ($questionAnswerData->answers as $answer) {
            $singleQuestionId = $answer->single_question_id;
            $isCorrect = $question->options()->where('id', $answer->answer_id)->where('general_exam_question_question_id', $singleQuestionId)->exists();
            $isCorrectQuestionArray[] = $isCorrect;
            $answersData[] = [
                'general_exam_option_id' => $answer->answer_id,
                'is_correct_answer' => $isCorrect,
                'question_id' => $question->id,
                'student_id'    =>  $this->user->student->id,
                'single_question_id' => $answer->single_question_id
            ];
        }

        if (in_array(false, $isCorrectQuestionArray, true) or ($question->questions()->count() != count($isCorrectQuestionArray))) {
            $isCorrectQuestion = false;
        } elseif (in_array(true, $isCorrectQuestionArray, true)) {
            $isCorrectQuestion = true;

        }

        $questionAnswer =  [
            'student_id'    =>  $this->user->student->id,
            'general_exam_id'    =>  $question->general_exam_id,
            'general_exam_question_id'    =>  $question->id,
            'general_exam_option_id'    =>  null,
            'answer_text'    =>  null,
            'is_correct'    =>  $isCorrectQuestion,
            'details' => $answersData
        ];
        return $questionAnswer;
        throw new ErrorResponseException("api.Unable to define answer");
    }
    protected function multipleMatchingAnswer($question, $questionAnswerData)
    {
        $answersData =[];
        $isCorrectQuestionArray = [];
        $isCorrectQuestion = false;
        foreach ($questionAnswerData->answers as $answer) {
            $singleQuestionId = $answer->single_question_id;
            $isCorrect = $question->multiMatchingOptions()->wherePivot('general_exam_option_id', $answer->answer_id)->wherePivot('general_exam_question_question_id', $singleQuestionId)->exists();
            $isCorrectQuestionArray[] = $isCorrect;
            $answersData[] = [
                'general_exam_option_id' => $answer->answer_id,
                'is_correct_answer' => $isCorrect,
                'question_id' => $question->id,
                'student_id'    =>  $this->user->student->id,
                'single_question_id' => $answer->single_question_id
            ];
        }
        $allAnswersCount = $this->getAllMultiMatchingQuestionAnswerCount($question);
        if (in_array(false, $isCorrectQuestionArray, true) or $allAnswersCount != count($answersData)) {
            $isCorrectQuestion = false;
        } elseif (in_array(true, $isCorrectQuestionArray, true)) {
            $isCorrectQuestion = true;
        }


        $questionAnswer =  [
            'student_id'    =>  $this->user->student->id,
            'general_exam_id'    =>  $question->general_exam_id,
            'general_exam_question_id'    =>  $question->id,
            'general_exam_option_id'    =>  null,
            'answer_text'    =>  null,
            'is_correct'    =>  $isCorrectQuestion,
            'details' => $answersData
        ];
        return $questionAnswer;
        throw new ErrorResponseException("api.Unable to define answer");
    }

    public function getAllMultiMatchingQuestionAnswerCount($question)
    {
        $sum = 0;
        $questions = $question->questions;
        foreach ($questions as $question) {
            $sum += $question->multiMatchingOptions()->count();
        }
        return $sum;
    }
}
