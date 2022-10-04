<?php


namespace App\OurEdu\Quizzes\UseCases\HomeWorkUseCase;


use App\Exceptions\OurEduErrorException;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeWorkUseCase implements HomeWorkUseCaseInterface
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

    public function createHomeWork($data): array
    {
        $additionalData['created_by'] = $this->user->id;
        $additionalData['creator_role'] = $this->user->type;
        $classRoomClassSession = ClassroomClassSession::with('VCRSession')->findOrFail($data->classroom_class_session_id);

        $additionalData['subject_id'] = $classRoomClassSession->subject_id;
        $additionalData['classroom_id'] = $classRoomClassSession->classroom_id;
        $additionalData['classroom_class_id'] = $classRoomClassSession->classroom_class_id;
        $additionalData['vcr_session_id'] =  $classRoomClassSession->vcrSession ? $classRoomClassSession->vcrSession->id : null;
        $additionalData['quiz_title'] = $classRoomClassSession->subject['name'].'-'.$classRoomClassSession->instructor['name'];



        $useCase['quiz'] =  $this->quizRepository->create(array_merge($data->toArray(), $additionalData));
        $useCase['meta'] = [
            'message' => trans('api.Quiz created successfully')
        ];
        $useCase['status'] = 200;
        return $useCase;
    }

    public function editHomeWork($homework, $data): array
    {
        if ($homework->published_at) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('api.cant edit published quiz');
            $useCase['title'] = 'cant edit published quiz';
            return $useCase;
        }

        $additionalData = [];
            if ($homework->classroom_class_session_id != $data['classroom_class_session_id']) {
                $classRoomClassSession = ClassroomClassSession::with('VCRSession')->findOrFail($data['classroom_class_session_id']);

                $additionalData['subject_id'] = $classRoomClassSession->subject_id;
                $additionalData['classroom_id'] = $classRoomClassSession->classroom_id;
                $additionalData['classroom_class_id'] = $classRoomClassSession->classroom_class_id;
                $additionalData['vcr_session_id'] =  $classRoomClassSession->vcrSession ? $classRoomClassSession->vcrSession->id : null;
            }
            $data = array_merge($data,$additionalData);

        if ($this->quizRepository->setQuiz($homework)->update($data)){
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

    public function getHomeWork($homeworkId): array
    {
        $useCase['quiz'] = $this->quizRepository->findOrFail($homeworkId);
        return $useCase;
    }

    public function updateHomeWorkQuestions($homework, $data)
    {
        if (!is_null($homework->published_at)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('api.cant edit published quiz');
            $useCase['title'] = 'cant edit published quiz';
            return $useCase;
        }

        $useCase = $this->questionRepository->createOrUpdateQuestions($homework->id, $data);
        return $useCase;
    }

    public function publishHomework($homeworkId){
        try{
            $homework = $this->quizRepository->findOrFailByMultiFields($homeworkId,['quiz_type'=>'homework']);
            if ((new \Carbon\Carbon($homework->start_at))->subHour() < now()) {
                if (new \Carbon\Carbon($homework->start_at) < now()) {
                    return formatErrorValidation([
                        'status' => 422,
                        'title' => 'The periodic test has passed',
                        'detail' => trans('api.The homework already started')
                    ], 422);
                }
                return formatErrorValidation([
                    'status' => 422,
                    'title' => 'The periodic test has passed',
                    'detail' => trans('api.Can not update homework which will start in less than one hour')
                ], 422);
            }
            if($homework->questions()->count() > 0) {
                if (!is_null($homework->published_at)) {
                    return formatErrorValidation([
                        'status' => 422,
                        'title' => 'Already Published',
                        'detail' => trans('api.HomeWork Already published')
                    ], 422);
                }
                if ($this->quizRepository->setQuiz($homework)->update(['published_at' => now()])) {
                    $homework->allStudentQuiz()->update(['published_at' => now()]);
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
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

}
