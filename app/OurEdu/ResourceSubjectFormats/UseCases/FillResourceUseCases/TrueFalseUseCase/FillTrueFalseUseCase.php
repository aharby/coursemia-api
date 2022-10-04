<?php


namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\TrueFalseUseCase;


use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestionMedia;
use App\OurEdu\ResourceSubjectFormats\Repository\ResourceSubjectFormatSubjectRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse\TrueFalseRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse\TrueFalseRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class FillTrueFalseUseCase implements FillTrueFalseUseCaseInterface
{
    private $trueFalseRepository;
    private $resourceSubjectFormatSubjectRepo;


    public function __construct(
        TrueFalseRepositoryInterface $trueFalseRepository,
        ResourceSubjectFormatSubjectRepositoryInterface $resourceSubjectFormatSubjectRepository
    )
    {
        $this->trueFalseRepository = $trueFalseRepository;
        $this->resourceSubjectFormatSubjectRepo = $resourceSubjectFormatSubjectRepository;
    }

    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     */
    public function fillResource(int $resourceSubjectFormatId, $data, User $user)
    {

        $resourceSubjectFormatSubjectData = $data->resource_subject_format_subject_data;
        $acceptanceCriteria = json_decode($this->resourceSubjectFormatSubjectRepo->findOrFail($resourceSubjectFormatId)->accept_criteria, true);
        $trueFalseTypeId = $acceptanceCriteria['true_false_type'];
        $questions = $resourceSubjectFormatSubjectData->questions;
        $trueFalseData = [
            'description' => $resourceSubjectFormatSubjectData->description,
            'resource_subject_format_subject_id' => $resourceSubjectFormatId,
            'true_false_type' => $trueFalseTypeId,
            'question_type' => $resourceSubjectFormatSubjectData->question_type,
        ];

        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();

        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {
            $trueFalse = $this->trueFalseRepository->create($trueFalseData);

        } else {
            $trueFalse = $this->trueFalseRepository->findOrFail($resourceSubjectFormatSubjectDataId);

        }
        $trueFalseRepo = new TrueFalseRepository($trueFalse);

        $trueFalseRepo->update(['description' => $resourceSubjectFormatSubjectData->description]);

        $this->deleteQuestions($trueFalseRepo, $questions);

        $this->createOrUpdateQuestions($trueFalseRepo, $trueFalse->id, $questions);


    }

    private function deleteQuestions(TrueFalseRepository $trueFalseRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $trueFalseRepo->getQuestionsIds();
        $deleteIds = array_diff($oldQuestionsIds, $newIds);

        // deleting media data for the deleted questions
        foreach ($deleteIds as $questionId) {
            $questionObj = $trueFalseRepo->findQuestionOrFail($questionId);
            if ($questionObj->media) {
                deleteQuestionMedia($questionObj->media);
            }
        }

        $deleteIds = array_diff($oldQuestionsIds, $newIds);

        $trueFalseRepo->deleteQuestionsIds($deleteIds);

    }

    private function createOrUpdateQuestions(TrueFalseRepository $trueFalseRepo, $trueFalseId, $questions)
    {
        foreach ($questions as $question) {
            $questionId = $question->id;

            $questionData = [
                'text' => $question->text ?? null,
                'image' => $question->image ?? null,
                'question_feedback' => $question->question_feedback ?? null,
                'res_true_false_data_id' => $trueFalseId,
                'is_true' => (int)$question->is_true,
                'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
            ];
            if (Str::contains($questionId, 'new')) {
                $questionObj = $trueFalseRepo->createQuestion($questionData);
            } else {
                $questionObj = $trueFalseRepo->updateQuestion($questionId, $questionData);
            }
            $this->createOrUpdateOptions($trueFalseRepo, $questionObj->id, $question);

            attachQuestionMeida($question, $questionObj, 'subject/true_false');

        }

    }

    private function createOrUpdateOptions(TrueFalseRepository $trueFalseRepo, $questionId, $question)
    {
        $options = $question->options ?? [];

        $optionsDataMultiple = [];
        $this->deleteOptions($trueFalseRepo, $questionId, $options);

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
                $trueFalseRepo->updateOption($optionId, $optionData);
            }
        }

        if (count($optionsDataMultiple) > 0) {
            $insert = $trueFalseRepo->insertMultipleOptions($optionsDataMultiple);
        }
    }

    private function deleteOptions(TrueFalseRepository $trueFalseRepo, $questionId, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldQuestionsIds = $trueFalseRepo->getQuestionOptionsIds($questionId);

        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $trueFalseRepo->deleteOptions($questionId, $deleteIds);

    }


}
