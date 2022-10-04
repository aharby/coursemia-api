<?php

namespace App\OurEdu\Assessments\Repositories\AssessmentQuestions\MatrixQuestion;


use App\OurEdu\Assessments\Models\Questions\Matrix\AssessmentMatrixData;
use App\OurEdu\Assessments\Models\Questions\Matrix\AssessmentMatrixColumn;
use App\OurEdu\Assessments\Models\Questions\Matrix\AssessmentMatrixRow;

class MatrixQuestionRepository implements MatrixQuestionRepositoryInterface
{

    private $assessmentMatixData;

    public function __construct(AssessmentMatrixData $assessmentMatixData)
    {
        $this->assessmentMatixData = $assessmentMatixData;
    }

    public function findOrFail(int $id): ?AssessmentMatrixData
    {
        return $this->assessmentMatixData->findOrFail($id);
    }

    public function findQuestionOrFail(int $id) :? AssessmentMatrixData{
        return AssessmentMatrixData::findOrFail($id);
    }

    public function createQuestion($data)
    {
        return $this->assessmentMatixData->create($data);
    }

    public function updateQuestion($questionId, $data)
    {
        $update = $this->assessmentMatixData->where('id', $questionId)->update($data);

        if ($update) {
            return $this->assessmentMatixData->where('id', $questionId)->firstOrFail();
        }
        return null;
    }


    public function insertMultipleColumns($data)
    {
        return AssessmentMatrixColumn::insert($data);
    }

    public function updateColumns($columnsId, $data)
    {
        return AssessmentMatrixColumn::where('id', $columnsId)->update($data);
    }


    public function getQuestionColumnsIds($questionId)
    {
        $question = $this->assessmentMatixData->find($questionId);

        if ($question) {
            return $question->columns()->pluck('id')->toArray();
        }
        return [];
    }

    public function deleteColumns($questionId, $columnsIds)
    {
        $question = $this->assessmentMatixData->find($questionId);

        if ($question) {
            return $question->columns()->whereIn('id', $columnsIds)->delete();
        }
        return false;
    }


    public function getQuestionRowsIds($questionId)
    {
        $question = $this->assessmentMatixData->find($questionId);

        if ($question) {
            return $question->rows()->pluck('id')->toArray();
        }
        return [];
    }


    public function insertMultipleRows($data)
    {
        return AssessmentMatrixRow::insert($data);
    }

    public function updateRows($questionsId, $data)
    {
        return AssessmentMatrixRow::where('id', $questionsId)->update($data);
    }


    public function getRowsIds($questionId)
    {
        $question = $this->assessmentMatixData->find($questionId);

        if ($question) {
            return $question->rows()->pluck('id')->toArray();
        }
        return [];
    }

    public function deleteRows($questionId, $rowsIds)
    {
        $question = $this->assessmentMatixData->find($questionId);

        if ($question) {
            return $question->rows()->whereIn('id', $rowsIds)->delete();
        }
        return false;
    }
}