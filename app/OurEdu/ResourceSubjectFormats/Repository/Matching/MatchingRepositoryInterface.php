<?php


namespace App\OurEdu\ResourceSubjectFormats\Repository\Matching;



use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingQuestion;

interface MatchingRepositoryInterface
{

    public function findOrFail(int $id): ?MatchingData;

    public function findQuestionOrFail(int $id): ?MatchingQuestion;

    public function create(array $data): MatchingData;

    public function createQuestion($data);

    public function insertMultipleOptions($data);
}
