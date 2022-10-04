<?php


namespace App\OurEdu\ResourceSubjectFormats\Repository\MultiMatching;



use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingQuestion;

interface MultiMatchingRepositoryInterface
{

    public function findOrFail(int $id): ?MultiMatchingData;

    public function findQuestionOrFail(int $id): ?MultiMatchingQuestion;

    public function create(array $data): MultiMatchingData;

    public function createQuestion($data);

    public function insertMultipleOptions($data);
}
