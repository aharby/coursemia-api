<?php

declare(strict_types=1);

namespace App\OurEdu\ResourceSubjectFormats\Repository\DragDrop;


use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;

interface DragDropRepositoryInterface
{
    /**
     * @param int $id
     * @return ResourceSubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?DragDropData;

    public function findQuestionOrFail(int $id): ?DragDropQuestion;

    public function create(array $data): DragDropData;

    public function createQuestion($data);

    public function getDragDropDateBySubjectFormatId($resourceSubjectFormatSubjectId): ?DragDropData;

}
