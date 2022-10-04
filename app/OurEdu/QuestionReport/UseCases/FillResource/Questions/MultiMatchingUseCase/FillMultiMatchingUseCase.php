<?php


namespace App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MultiMatchingUseCase;


use App\OurEdu\ResourceSubjectFormats\Repository\MultiMatching\MultiMatchingRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\MultiMatching\MultiMatchingRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FillMultiMatchingUseCase implements FillMultiMatchingUseCaseInterface
{
    private $multiMatchingRepository;


    public function __construct(MultiMatchingRepositoryInterface $multiMatchingRepository)
    {
        $this->multiMatchingRepository = $multiMatchingRepository;
    }

    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     */
    public function fillResource(int $dataId, $data)
    {

        $questions = $data->questions;
        $options = $data->options;
        $matchingData = [
            'description' => $data->description,
        ];


        $multiMatching = $this->multiMatchingRepository->findOrFail($dataId);

        $multiMatchingRepo = new MultiMatchingRepository($multiMatching);

        $multiMatchingRepo->update($matchingData);


        $this->deleteQuestions($multiMatchingRepo, $questions);

        $this->deleteOptions($multiMatchingRepo , $options);

        //Should Return Array Of mapping between new_id and real database ids
        $questionMap = $this->createOrUpdateQuestions($multiMatchingRepo, $multiMatching->id, $questions);

        $this->createOrUpdateOptions($multiMatchingRepo, $multiMatching->id, $options , $questionMap);

        return $this->multiMatchingRepository->findOrFail($dataId);

    }

    private function createOrUpdateQuestions(MultiMatchingRepository $multiMatchingRepo, $matchingId, $questions)
    {
        $questionMap = [];
        foreach ($questions as $question) {
            $questionId = $question->id;
            $questionData = [
                'text' => $question->text ?? null,
                'res_multi_matching_data_id' => $matchingId,
            ];
            if (Str::contains($questionId, 'new')) {
                $questionObj = $multiMatchingRepo->createQuestion($questionData);
                $questionMap[$questionId] = $questionObj->id;
            } else {
                $questionObj = $multiMatchingRepo->updateQuestionData($questionId, $questionData);
            }
            attachQuestionMeida($question, $questionObj, 'subject/multi_matching');

        }
        return $questionMap;

    }

    private function deleteQuestions(MultiMatchingRepository $multiMatchingRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $multiMatchingRepo->getQuestionsIds();

        $deleteIds = array_diff($oldQuestionsIds, $newIds);

        $multiMatchingRepo->deleteQuestionsIds($deleteIds);

    }

    private function deleteOptions(MultiMatchingRepository $multiMatchingRepo, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldOptionsIds = $multiMatchingRepo->getAllQuestionOptionsIds();
        $deleteIds = array_diff($oldOptionsIds, $newIds);

        $multiMatchingRepo->deleteOptionsIds($deleteIds);

    }

    private function createOrUpdateOptions(MultiMatchingRepository $multiMatchingRepo, $matchingId, $options , $questionMap)
    {
//        $optionsDataMultiple = [];
        foreach ($options as $option) {

            $questions = $this->replaceNewWithIds($option->questions , $questionMap);

            $optionId = $option->id;
            $optionData = [
                'option' => $option->option,
                'res_multi_matching_data_id' => $matchingId,
            ];

            if (Str::contains($optionId, 'new')) {
               $option = $multiMatchingRepo->insertOption($optionData);
            } else {
                $multiMatchingRepo->updateOption($optionId, $optionData);
               $option = $multiMatchingRepo->findOption($optionId);
            }

            $option->questions()->sync($questions);
        }

    }

    public function replaceNewWithIds($questions , $questionMap) {
        foreach ($questions as $key => $question) {
            if (array_key_exists($question , $questionMap)){
                $questions[$key] = $questionMap[$question];
            }
        }
        return $questions;
    }
}
