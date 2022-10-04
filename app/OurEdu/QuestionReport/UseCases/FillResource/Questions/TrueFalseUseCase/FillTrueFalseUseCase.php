<?php


namespace App\OurEdu\QuestionReport\UseCases\FillResource\Questions\TrueFalseUseCase;


use App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse\TrueFalseRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse\TrueFalseRepositoryInterface;

use App\OurEdu\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class FillTrueFalseUseCase implements FillTrueFalseUseCaseInterface
{
    private $trueFalseRepository;


    public function __construct(TrueFalseRepositoryInterface $trueFalseRepository)
    {
        $this->trueFalseRepository = $trueFalseRepository;
    }

    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     */
    public function fillResource(int $questionId, $data)
    {
        if($data->text || $data->image ) {
            $questionData = [
                'text' => $data->text ?? null,
                'image' => $data->image ?? null,
            ];
            $this->updateQuestion($questionId , $questionData);

            $this->createOrUpdateOptions($questionId, $data->options);

        } else {
            $this->trueFalseRepository->deleteALlQuestionOptions($questionId);
            $this->trueFalseRepository->deleteQuestionsWithoutData([$questionId]);
        }
        return $this->trueFalseRepository->findQuestionOrFail($questionId);
    }

    private function updateQuestion($questionId, $questionData)
    {
       $this->trueFalseRepository->updateQuestionWithoutData($questionId, $questionData);
    }

    private function createOrUpdateOptions($questionId, $options)
    {

        $optionsDataMultiple = [];
        $this->deleteOptions($questionId, $options);

        foreach ($options as $option) {

            $optionId = $option->id;
            $optionData = [
                'option' => $option->option,
                'is_correct_answer' => $option->is_correct,
                'res_true_false_question_id' => $questionId,
            ];
            if (Str::contains($optionId, 'new')) {
                $optionsDataMultiple[] = $optionData;
            } else {
                $this->trueFalseRepository->updateOption($optionId, $optionData);
            }
        }

        if (count($optionsDataMultiple) > 0) {
           $insert= $this->trueFalseRepository->insertMultipleOptions($optionsDataMultiple);
        }
    }

    private function deleteOptions($questionId, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldQuestionsIds = $this->trueFalseRepository->getQuestionOptionsIdsWithoutData($questionId);

        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $this->trueFalseRepository->deleteQuestionOptions($questionId, $deleteIds);
    }

}
