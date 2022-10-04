<?php

namespace App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MultipleChoiceUseCase;

use App\OurEdu\ResourceSubjectFormats\Repository\MultipleChoice\MultipleChoiceRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\MultipleChoice\MultipleChoiceRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\ResourceSubjectFormatSubjectRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FillMultipleChoiceUseCase implements FillMultipleChoiceUseCaseInterface
{
    private $multipleChoiceRepository;
    private $resourceSubjectFormatSubjectRep;


    public function __construct(MultipleChoiceRepositoryInterface $multipleChoiceRepository,ResourceSubjectFormatSubjectRepository $resourceSubjectFormatSubjectRepository)
    {
        $this->multipleChoiceRepository = $multipleChoiceRepository;
        $this->resourceSubjectFormatSubjectRep = $resourceSubjectFormatSubjectRepository;
    }

    public function fillResource(int $questionId, $data)
    {


        if ($data->question) {
            $questionData = [
                'question' => $data->question ?? null,
            ];

            $this->updateQuestion($questionId, $questionData);

            $this->createOrUpdateOptions($questionId , $data->options);

        } else {

            $this->multipleChoiceRepository->deleteAllOptions($questionId);
            $this->multipleChoiceRepository->deleteQuestionWithoutData($questionId);
        }
        return $this->multipleChoiceRepository->findQuestionOrFail($questionId);
    }


    private function updateQuestion(
        $questionId,
        $questionData
    ) {
        $this->multipleChoiceRepository->updateQuestionWithoutData($questionId, $questionData);
    }

    private function createOrUpdateOptions($questionId, $options)
    {

        $optionsDataMultiple = [];
        $this->deleteOptions($questionId, $options);

        foreach ($options as $option) {

            $optionId = $option->id;
            $optionData = [
                'answer' => $option->option,
                'is_correct_answer' => $option->is_correct_answer,
                'res_multiple_choice_question_id' => $questionId,
            ];

            if (Str::contains($optionId, 'new')) {
                $optionsDataMultiple[] = $optionData;
            } else {
                $this->multipleChoiceRepository->updateOption($optionId, $optionData);
            }
        }

        if (count($optionsDataMultiple) > 0) {
            $insert = $this->multipleChoiceRepository->insertMultipleOptions($optionsDataMultiple);
        }
    }

    private function deleteOptions($questionId, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldQuestionsIds = $this->multipleChoiceRepository->getQuestionOptionsIdsWithoutData($questionId);

        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $this->multipleChoiceRepository->deleteOptionsWithoutData($questionId, $deleteIds);
    }

}
