<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases;

use App\OurEdu\BaseNotification\Jobs\CalculateGeneralAverageGradesAndCountStudentsJob;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsPublicStatusesEnums;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentAnswerRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepositoryInterface;

class GeneralQuizQuestionUseCase implements GeneralQuizQuestionUseCaseInterface
{
    /**
     * @var GeneralQuizMultipleChoiceUseCaseInterface
     */
    private $generalQuizMultipleChoiceUseCase;

    /**
     * @var GeneralTrueFalseUseCaseInterface
     */
    private $generalTrueFalseUseCase;
    /**
     * @var TrueFalseQuestionWithCorrectUseCaseInterface
     */
    private $trueFalseQuestionWithCorrectUseCase;

    private $essayUseCase;
    private $dragDropUseCase;
    private $generalQuizStudentAnswerRepository;
    private $generalQuizStudentRepository;
    private $completeQuestionUseCase;
    /**
     * @var QuestionBankRepositoryInterface
     */
    private $questionBankRepository;

    /**
     * GeneralQuizQuestionUseCase constructor.
     * @param GeneralQuizMultipleChoiceUseCaseInterface $generalQuizMultipleChoiceUseCase
     * @param GeneralTrueFalseUseCaseInterface $generalTrueFalseUseCase
     * @param TrueFalseQuestionWithCorrectUseCaseInterface $trueFalseQuestionWithCorrectUseCase
     * @param GeneralEssayCaseInterface $essayUseCase
     * @param GeneralQuizStudentAnswerRepositoryInterface $generalQuizStudentAnswerRepository
     * @param GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository
     * @param QuestionBankRepositoryInterface $questionBankRepository
     */
    public function __construct(
        GeneralQuizMultipleChoiceUseCaseInterface $generalQuizMultipleChoiceUseCase,
        GeneralTrueFalseUseCaseInterface $generalTrueFalseUseCase,
        TrueFalseQuestionWithCorrectUseCaseInterface $trueFalseQuestionWithCorrectUseCase,
        GeneralEssayCaseInterface $essayUseCase,
        GeneralQuizDragDropUseCaseInterface $dragDropUseCase,
        GeneralQuizStudentAnswerRepositoryInterface $generalQuizStudentAnswerRepository,
        GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository,
        CompleteQuestionUseCaseInterface $completeQuestionUseCase,
        QuestionBankRepositoryInterface $questionBankRepository
    ) {
        $this->completeQuestionUseCase = $completeQuestionUseCase;
        $this->trueFalseQuestionWithCorrectUseCase = $trueFalseQuestionWithCorrectUseCase;
        $this->generalQuizMultipleChoiceUseCase = $generalQuizMultipleChoiceUseCase;
        $this->generalTrueFalseUseCase = $generalTrueFalseUseCase;
        $this->essayUseCase = $essayUseCase;
        $this->dragDropUseCase = $dragDropUseCase;
        $this->generalQuizStudentAnswerRepository = $generalQuizStudentAnswerRepository;
        $this->generalQuizStudentRepository = $generalQuizStudentRepository;
        $this->questionBankRepository = $questionBankRepository;
    }

    public function addQuestion(GeneralQuiz $generalQuiz, $data)
    {
        $validationErrors = $this->validateAddQuestions($generalQuiz, $data);
        if ($validationErrors) {
            return $validationErrors;
        }
        if (!is_null($data['id']) && $data['id'] != '') {
            $this->updateQuestionBank($data);
        }

        $this->setCourseHomeWorkData($generalQuiz,$data);

        switch ($data->question_slug) {
            case QuestionsTypesEnums::MULTI_CHOICE:
                return $this->generalQuizMultipleChoiceUseCase->addQuestion($generalQuiz, $data);
            case QuestionsTypesEnums::TRUE_FALSE;
                return $this->generalTrueFalseUseCase->addQuestion($generalQuiz, $data);
            case QuestionsTypesEnums::SINGLE_CHOICE:
                return $this->generalQuizMultipleChoiceUseCase->addQuestion($generalQuiz, $data);
                break;
            case QuestionsTypesEnums::TRUE_FALSE_WITH_CORRECT:
                return $this->trueFalseQuestionWithCorrectUseCase->addQuestion($generalQuiz, $data);
                break;
            case QuestionsTypesEnums::ESSAY:
                return $this->essayUseCase->addQuestion($generalQuiz, $data);
                break;
            case QuestionsTypesEnums::DRAG_DROP_TEXT:
            case QuestionsTypesEnums::DRAG_DROP_IMAGE:
                return $this->dragDropUseCase->addQuestion($generalQuiz, $data);
            case QuestionsTypesEnums::COMPLETE:
                return $this->completeQuestionUseCase->addQuestion($generalQuiz, $data);
                break;
            default:
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => "Question Type not Supported",
                    'detail' => "Question Type not Supported",
                ];

                return $errors;
        }
    }

    public function reviewEssay(GeneralQuiz $generalQuiz, GeneralQuizStudentAnswer $answer, $request_data)
    {
        $studentQuiz = $this->generalQuizStudentRepository->findStudentGeneralQuiz(
            $generalQuiz->id,
            $answer->student_id
        );

        $validationErrors = $this->validateEditGeneralQuiz($answer, $request_data, $studentQuiz);
        if ($validationErrors) {
            return $validationErrors;
        }

        $data['score'] = $request_data['score'];
        if ((float)$request_data['score'] > 0) {
            $data['is_correct'] = true;
        }
        $data['is_reviewed'] = true;
        $this->generalQuizStudentAnswerRepository->update($answer, $data);

        $mark = $generalQuiz->mark;
        $score = $this->generalQuizStudentRepository->getStudentCorrectAnswersScore(
            $generalQuiz->id,
            $answer->student_id
        );
        $score_percentage = $mark ? ($score / $mark) * 100 : 0;

        $data = [
            'score_percentage' => number_format($score_percentage, 2, '.', ''),
            'score' => $score,
            'is_reviewed' => true
        ];
        $this->generalQuizStudentRepository->update($studentQuiz->id, $data);

        //Average Counter Job
        CalculateGeneralAverageGradesAndCountStudentsJob::dispatch($generalQuiz);

        $hasUnReviewedEssayQuestions = $this->generalQuizStudentAnswerRepository->hasUnReviewedEssayQuestions(
            $generalQuiz->id
        );

        if (!$hasUnReviewedEssayQuestions) {
            $generalQuizStudent = $this->generalQuizStudentRepository->findStudentGeneralQuiz(
                $generalQuiz->id,
                $answer->student_id
            );
            $this->generalQuizStudentRepository->update($generalQuizStudent->id, ['is_reviewed' => true]);
        }
        $useCase['answer'] = $answer;
        $useCase['status'] = 200;
        $useCase['message'] = trans('general_quizzes.essay reviewed Successfully');
        return $useCase;
    }

    private function updateQuestionBank($data)
    {
        $record = [
            'subject_format_subject_id' => $data['attributes']['section_id'] ?? null,
            'grade' => $data['attributes']['grade'],
            'question_slug' => $data['attributes']['question_slug'],
        ];
        $this->questionBankRepository->findOrFail($data['id'])->update($record);
    }

    private function validateEditGeneralQuiz($answer, $data, $studentQuiz)
    {
        if (!isset($data->score)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.score is required');
            $useCase['title'] = 'score is required';
            return $useCase;
        }
        if ($data->score > $answer->questionBank->grade) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.score is high');
            $useCase['title'] = 'the score is bigger than full mark';
            return $useCase;
        }
        if (!$studentQuiz->is_finished && $studentQuiz->generalQuiz->end_at >= now()) {
            $quizType = trans('general_quizzes.' . $studentQuiz->generalQuiz->quiz_type);
            $useCase['status'] = 422;
            $useCase['detail'] = trans("general_quizzes.general quiz is not finished yet", [
                'quiz_type' => $quizType
            ]);
            $useCase['title'] = $studentQuiz->generalQuiz->quiz_type . ' is not finished yet';
            return $useCase;
        }
    }

    private function validateAddQuestions(GeneralQuiz $generalQuiz, $data)
    {
        if (count($generalQuiz->studentsAnswered)) {
            return formatErrorValidation([
                'status' => 422,
                'title' => 'cant edit attended homework',
                'detail' => trans('general_quizzes.cant edit attended general quiz')
            ], 422);
        }

        $quizType = trans('general_quizzes.' . $generalQuiz->quiz_type);
        if (!$generalQuiz->is_active) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.inactive general quiz', [
                'quiz_type' => $quizType
            ]);
            $useCase['title'] = $generalQuiz->quiz_type . ' is inactive';
            $errors['errors'][] = $useCase;
            return $errors;
        }


        // quiz time passed
        if (!is_null($generalQuiz->end_at)) {
            if (now() > $generalQuiz->end_at) {
                $useCase['status'] = 422;
                $useCase['detail'] = trans('general_quizzes.quiz time passed', [
                    'quiz_type' => $quizType
                ]);
                $useCase['title'] = $generalQuiz->quiz_type . ' time passed';
                $errors['errors'][] = $useCase;
                return $errors;
            }
        }

        if (isset($data->section_id) and !is_null($generalQuiz->subject_id)  and !in_array($data->section_id, $generalQuiz->subject_sections)) {
            $errors['errors'][] = [
                'status' => 422,
                'title' => 'sectionId is not valid',
                'detail' => trans('general_quizzes.sectionId is not valid data')
            ];
            return $errors;
        }
    }

    private function setCourseHomeWorkData(GeneralQuiz $generalQuiz, $data)
    {
        if ($generalQuiz->quiz_type == GeneralQuizTypeEnum::COURSE_HOMEWORK) {
            foreach ($data->generalQuizQuestionsData->questions as $key => $question) {
                $question->public_status = false;
            }
            $data->section_id = null;
            $data->subject_id = null;
            return $data;
        }

        return $data;
    }

}
