<?php


namespace App\OurEdu\Quizzes\UseCases\PeriodicTestUseCase;


use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Support\Facades\Auth;

class PeriodicTestUseCase implements PeriodicTestUseCaseInterface
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

    public function createPeriodicTest($data): array
    {
        $additionalData['created_by'] = $this->user->id;
        $additionalData['creator_role'] = $this->user->type;

        $additionalData['quiz_title'] = Subject::findOrFail($data->subject_id)->name.'-'.$this->user->name;


        $useCase['quiz'] =  $this->quizRepository->create(array_merge($data->toArray(), $additionalData));
        $useCase['meta'] = [
            'message' => trans('api.Periodic Test created successfully')
        ];
        $useCase['status'] = 200;
        return $useCase;
    }

    public function editPeriodicTest($periodicTestId, $data): array
    {
        if ($periodicTestId->published_at) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('api.cant edit published quiz');
            $useCase['title'] = 'cant edit published quiz';
            return $useCase;
        }

        if ($this->quizRepository->setQuiz($periodicTestId)->update($data)){
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

    public function getPeriodicTest($periodicTestId): array
    {
        $useCase['quiz'] = $this->quizRepository->findOrFail($periodicTestId);
        return $useCase;
    }

    public function updatePeriodicTestQuestions($periodicTestId, $data)
    {
        if (!is_null($periodicTestId->published_at)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('api.cant edit published quiz');
            $useCase['title'] = 'cant edit published quiz';
            return $useCase;
        }

        $useCase = $this->questionRepository->createOrUpdateQuestions($periodicTestId->id, $data);
        return $useCase;
    }

}
