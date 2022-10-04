<?php


namespace App\OurEdu\ResourceSubjectFormats\Repository\HotSpot;



use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotQuestion;

interface HotSpotRepositoryInterface
{

    public function findOrFail(int $id): ?HotSpotData;

    public function create(array $data): HotSpotData;

    public function createQuestion($data) : HotSpotQuestion;

    public function updateQuestion($questionId , $data) : ?HotSpotQuestion;

    public function deleteQuestionsIds($deleteIds);

    public function getAnswersIds($questionId);

    public function deleteAnswersIds($deleteIds);

    public function insertAnswer($data) : HotSpotAnswer;

    public function updateAnswer($answerId , $data) : ?HotSpotAnswer;

    public function findAnswer($answerId) : ?HotSpotAnswer;

    public function getQuestionsIds();
}
