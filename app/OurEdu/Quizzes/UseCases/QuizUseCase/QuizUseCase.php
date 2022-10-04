<?php

namespace App\OurEdu\Quizzes\UseCases\QuizUseCase;

use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use Illuminate\Support\Facades\Auth;

class QuizUseCase implements QuizUseCaseInterface
{
    private $quizRepository;
    private $questionRepository;
    private $user;

    public function __construct(
        QuizRepositoryInterface $quizRepository
    )
    {
        $this->quizRepository = $quizRepository;
        $this->user = Auth::guard('api')->user();
    }

    public function createQuiz($data): array
    {
        $additionalData['created_by'] = $this->user->id;
        $additionalData['creator_role'] = $this->user->type;
        $additionalData['quiz_type'] = QuizTypesEnum::QUIZ;

        $classRoomClassSession = ClassroomClassSession::with('VCRSession')->findOrFail($data->classroom_class_session_id);
        $additionalData['classroom_id'] = $classRoomClassSession->classroom_id;
        $additionalData['classroom_class_id'] = $classRoomClassSession->classroom_class_id;
        $additionalData['vcr_session_id'] =  $classRoomClassSession->vcrSession ? $classRoomClassSession->vcrSession->id : null;
        $additionalData['subject_id'] = $classRoomClassSession->subject_id;
        $additionalData['quiz_title'] = $classRoomClassSession->subject['name'].'-'.$classRoomClassSession->instructor['name'];



        $useCase['quiz'] =  $this->quizRepository->create(array_merge($data->toArray(), $additionalData));
        $useCase['meta'] = [
            'message' => trans('api.Quiz created successfully')
        ];
        $useCase['status'] = 200;
        return $useCase;
    }

    public function editQuiz($quiz, $data): array
    {
        if ($quiz->published_at) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('api.cant edit published quiz');
            $useCase['title'] = 'cant edit published quiz';
            return $useCase;
        }

        $additionalData = [];
        if ($quiz->quiz_type == QuizTypesEnum::QUIZ) {
            if ($quiz->classroom_class_session_id != $data['classroom_class_session_id']) {
                $classRoomClassSession = ClassroomClassSession::with('VCRSession')->findOrFail($data['classroom_class_session_id']);

                $additionalData['classroom_id'] = $classRoomClassSession->classroom_id;
                $additionalData['classroom_class_id'] = $classRoomClassSession->classroom_class_id;
                $additionalData['vcr_session_id'] =  $classRoomClassSession->vcrSession ? $classRoomClassSession->vcrSession->id : null;
            }
            $data = array_merge($data,$additionalData);
        }

        if ($this->quizRepository->setQuiz($quiz)->update($data)){
            $useCase['quiz'] = $this->quizRepository->getQuiz();
        }else {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('api.error updating Quiz');
            $useCase['title'] = 'error updating Quiz';
            return $useCase;
        }
        $useCase['status'] = 200;
        $useCase['meta'] = [
            'message' => trans('api.Updated Successfully')
        ];
        return $useCase;
    }

    public function publishQuiz($quizId){
        $quiz = $this->quizRepository->findOrFailByMultiFields($quizId,['quiz_type'=>'quiz']);
        if($quiz->questions()->count() > 0) {
            if (!is_null($quiz->published_at)) {
                return formatErrorValidation([
                    'status' => 422,
                    'title' => 'Already Published',
                    'detail' =>trans('api.Quiz Already published')
                ], 422);
            }

            $session = $quiz->classroomSession;
            if ((new \Carbon\Carbon($session->from))->subHour() < now()) {
                if (new \Carbon\Carbon($session->from) < now()) {
                    return formatErrorValidation([
                        'status' => 422,
                        'title' => 'The quiz time has passed',
                        'detail' => trans('api.The quiz time has passed')
                    ], 422);
                }
            }

            if ($this->quizRepository->setQuiz($quiz)->update(['published_at' => now()])) {
                $quiz->allStudentQuiz()->update(['published_at' => now()]);
                return response()->json([
                    'meta' => [
                        'message' => trans('api.Published Successfully')
                    ]
                ]);
            }
        }
        return formatErrorValidation([
            'status' => 422,
            'title' => 'Cant Publish,you should put questions first',
            'detail' => trans('api.You should put questions first.')
        ], 422);

    }

    public function getQuiz($quizId): array
    {
        $useCase['quiz'] = $this->quizRepository->findOrFail($quizId);
        return $useCase;
    }

    public function updateQuizQuestions($quiz, $data)
    {
        if (!is_null($quiz->published_at)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('api.cant edit published quiz');
            $useCase['title'] = 'cant edit published quiz';
            return $useCase;
        }

        $useCase = $this->questionRepository->createOrUpdateQuestions($quiz->id, $data);
        return $useCase;
    }

}
