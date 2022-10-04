<?php

namespace App\OurEdu\Assessments\Repositories\AssessmentQuestions\MatrixQuestion;


use App\OurEdu\Assessments\Models\Questions\Matrix\AssessmentMatrixData;
use App\OurEdu\Assessments\Models\Questions\Matrix\AssessmentMatrixColumn;
use App\OurEdu\Assessments\Models\Questions\Matrix\AssessmentMatrixRow;

interface MatrixQuestionRepositoryInterface
{

    public function findOrFail(int $id): ?AssessmentMatrixData;

    public function findQuestionOrFail(int $id) :? AssessmentMatrixData;

    public function createQuestion($data);

    public function updateQuestion($questionId, $data);

    public function insertMultipleColumns($data);

    public function updateColumns($columnsId, $data);

    public function getQuestionColumnsIds($questionId);

    public function deleteColumns($questionId, $columnsIds);

    public function getQuestionRowsIds($questionId);

    public function insertMultipleRows($data);

    public function updateRows($questionsId, $data);

    public function getRowsIds($questionId);

    public function deleteRows($questionId, $rowsIds);
}