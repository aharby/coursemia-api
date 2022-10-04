<?php


namespace App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MatchingUseCase;


use App\OurEdu\ResourceSubjectFormats\Repository\Matching\MatchingRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Matching\MatchingRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FillMatchingUseCase implements FillMatchingUseCaseInterface
{

    private $matchingRepository;


    public function __construct(MatchingRepositoryInterface $matchingRepository)
    {
        $this->matchingRepository = $matchingRepository;
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

        $matching = $this->matchingRepository->findOrFail($dataId);

        $matchingRepo = new MatchingRepository($matching);
        $matchingRepo->update($matchingData);

        $this->deleteQuestions($matchingRepo, $questions);
        $this->deleteOptions($matchingRepo , $options);


        //Should Return Array Of mapping between new_id and real database ids
        $questionsMap = $this->createOrUpdateQuestions($matchingRepo, $matching->id, $questions);

        $this->createOrUpdateOptions($matchingRepo, $matching->id, $options , $questionsMap);

        return $this->matchingRepository->findOrFail($dataId);

    }

    private function createOrUpdateQuestions(MatchingRepository $matchingRepo, $matchingId, $questions)
    {
        $questionsMap = [];
        foreach ($questions as $question) {
            $questionId = $question->id;
            $questionData = [
                'text' => $question->text ?? null,
                'res_matching_data_id' => $matchingId,
            ];
            if (Str::contains($questionId, 'new')) {
                $questionObj = $matchingRepo->createQuestion($questionData);
                $questionsMap[$questionId] = $questionObj->id;
            } else {
                $questionObj = $matchingRepo->updateQuestionData($questionId, $questionData);
            }
            attachQuestionMeida($question, $questionObj, 'subject/matching');

        }
        return $questionsMap;
    }

    private function deleteQuestions(MatchingRepository $matchingRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $matchingRepo->getQuestionsIds();

        $deleteIds = array_diff($oldQuestionsIds, $newIds);

        $matchingRepo->deleteQuestionsIds($deleteIds);

    }

    private function deleteOptions(MatchingRepository $matchingRepo, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldOptionsIds = $matchingRepo->getQuestionOptionsIds();

        $deleteIds = array_diff($oldOptionsIds, $newIds);
        $matchingRepo->deleteOptionsIds($deleteIds);

    }

    private function createOrUpdateOptions(MatchingRepository $matchingRepo, $matchingId, $options , $questionsMap)
    {
        $optionsDataMultiple = [];
        foreach ($options as $option) {

            $optionId = $option->id;
            $questionId = $option->question_id;
            $optionData = [
                'option' => $option->option,
                'res_matching_question_id' => $questionId,
                'res_matching_data_id' => $matchingId,
            ];

            if (Str::contains($questionId, 'new')) {
                $optionData['res_matching_question_id'] =  $questionsMap[$questionId];
            } else {
                $optionData['res_matching_question_id'] =  $questionId;
            }

            if (Str::contains($optionId, 'new')) {
                $optionsDataMultiple[] = $optionData;
            } else {
                $matchingRepo->updateOption($optionId, $optionData);
            }
        }

        if (count($optionsDataMultiple) > 0) {
            $insert= $matchingRepo->insertMultipleOptions($optionsDataMultiple);
        }
    }
}
