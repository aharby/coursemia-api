<?php

namespace App\OurEdu\VCRSessions\General\Transformers\V2;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTimesEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSessions\General\Transformers\QuizzesTransformers\AfterSessionQuizTransformer;
use App\OurEdu\VCRSessions\General\Transformers\QuizzesTransformers\EduSupervisorQuizTransformer;
use App\OurEdu\VCRSessions\General\Transformers\QuizzesTransformers\PreSessionQuizTransformer;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrTypeDataTransform\VcrTypeDataTransform;
use League\Fractal\TransformerAbstract;

class GetSessionDataTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'preSessionQuiz',
        'afterSessionQuiz',
        'eduSupervisorQuiz',
    ];
    private VcrTypeDataTransform $vcrTypeDataTrnasfomer;

    public function __construct(VcrTypeDataTransform $vcrTypeDataTransform)
    {
        $this->vcrTypeDataTrnasfomer = $vcrTypeDataTransform;
    }

    public function transform()
    {
        return $this->vcrTypeDataTrnasfomer->getData();
    }

    public function includePreSessionQuiz(VCRSession $VCRSession)
    {
        $preSessionQuiz = $VCRSession->quizzes()
            ->where('quiz_type', QuizTypesEnum::QUIZ)
            ->where('quiz_time', QuizTimesEnum::PRE_SESSION)
            ->first();

        if ($preSessionQuiz) {
            return $this->item($preSessionQuiz, new PreSessionQuizTransformer(), ResourceTypesEnums::QUIZ);
        }
    }

    public function includeAfterSessionQuiz(VCRSession $VCRSession)
    {
        $afterSessionQuiz = $VCRSession->quizzes()
            ->where('quiz_type', QuizTypesEnum::QUIZ)
            ->where('quiz_time', QuizTimesEnum::AFTER_SESSION)
            ->first();

        if ($afterSessionQuiz) {
            return $this->item($afterSessionQuiz, new AfterSessionQuizTransformer(), ResourceTypesEnums::QUIZ);
        }
    }

    public function includeEduSupervisorQuiz(VCRSession $VCRSession)
    {
        $eduSupervisorQuiz = $VCRSession->quizzes()
            ->where('quiz_type', QuizTypesEnum::QUIZ)
            ->where('creator_role', UserEnums::EDUCATIONAL_SUPERVISOR)
            ->first();

        if ($eduSupervisorQuiz) {
            return $this->item($eduSupervisorQuiz, new EduSupervisorQuizTransformer(), ResourceTypesEnums::QUIZ);
        }
    }
}
