<?php


namespace App\OurEdu\Assessments\UseCases\QuestionsUseCases\MatrixUseCase;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\MatrixQuestion\MatrixQuestionRepository;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\MatrixQuestion\MatrixQuestionRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\AssessmentQuestionRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\OurEdu\Assessments\Models\Questions\Matrix\AssessmentMatrixData;

class MatrixUseCase implements MatrixUseCaseInterface
{
    /**
     * @var MatrixQuestionRepositoryInterface
     */
    private $matrixRepository;
    /**
     * @var AssessmentQuestionRepositoryInterface
     */
    private $assessmentQuestionRepo;

    private $matrixType;
    private $category_id;

    /**
     * MatrixUseCase constructor.
     * @param MatrixQuestionRepositoryInterface $matrixRepository
     * @param AssessmentQuestionRepositoryInterface $assessmentQuestionRepo
     */
    public function __construct(
        MatrixQuestionRepositoryInterface $matrixRepository,
        AssessmentQuestionRepositoryInterface $assessmentQuestionRepo
    )
    {
        $this->matrixRepository = $matrixRepository;
        $this->assessmentQuestionRepo = $assessmentQuestionRepo;
    }

    public function addQuestion(Assessment $assessment, $data)
    {
        $assessmentQuestions = $data->assessmentQuestions;
        $this->matrixType = $data->question_slug;
        $questions = $assessmentQuestions->questions;
        $this->category_id = $data->category_id;
        $error = $this->validateMatrixQuestion($questions,$data);
        if ($error) {
            return $error;
        }

        $matrixQuestion = $this->createOrUpdateQuestions($assessment, $questions,$data);

        return $matrixQuestion;
    }

    private function validateMatrixQuestion($questions,$data)
    {
        $errors = null;
        foreach ($questions as $question) {
            if(!property_exists($question,'question') || $question->question ==='' || ctype_space($question->question)){
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question_header_is_missing',
                    'detail' => trans('assessment.required', ['field' => trans('assessment.question_header')])
                ];
            }

            if(!isset($question->columns)){
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'columns_is_missing',
                    'detail' => trans('assessment.required', ['field' => trans('assessment.columns')])
                ];
            }else{
                if($question->no_of_columns > 6 || $question->no_of_columns <2){
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'column number must be  greater than 2 and less than 6',
                        'detail' => trans('assessment.column number must be  greater than 2 and less than 6')
                    ];
                }

                if (count($question->columns) != $question->no_of_columns) {
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'columns must be equal  to column number',
                        'detail' => trans('assessment.columns must be equal  to column number')
                    ];
                }

                foreach ($question->columns as $key => $column) {
                    if (!property_exists($column, 'text') || $column->text === '' || ctype_space($column->text)) {
                        $errors['errors'][] = [
                            'status' => 422,
                            'title' => 'text_is_missing',
                            'detail' => trans('assessment.text_required_in_column', ['num' => $key + 1])
                        ];
                    }

                    if(!property_exists($column, 'grade') || $column->grade === '' || !is_numeric($column->grade) || $column->grade < 0) {
                        $errors['errors'][] = [
                            'status' => 422,
                            'title' => 'grade_is_missing_or_invalid',
                            'detail' => trans('assessment.grade_required_numeric', ['num' => $key + 1])
                        ];
                    }


                }
            }

            if(!isset($question->rows)){
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'rows_is_missing',
                    'detail' => trans('assessment.required', ['field' => trans('assessment.rows')])
                ];
            }else{
                if($question->no_of_rows < 1){
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'row number must be  greater than 0',
                        'detail' => trans('assessment.row number must be  greater than 0')
                    ];
                }

                if (count($question->rows) != $question->no_of_rows) {
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'rows must be equal  to row number',
                        'detail' => trans('assessment.rows must be equal  to row number')
                    ];
                }
                foreach ($question->rows as $key => $row) {
                    if (!property_exists($row, 'text') || $row->text === '' || ctype_space($row->text)) {
                        $errors['errors'][] = [
                            'status' => 422,
                            'title' => 'text_is_missing',
                            'detail' => trans('assessment.text_required_in_row', ['num' => $key + 1, 'field' => trans('assessment.text')])
                        ];
                    }

                }
            }

        }

        return $errors;
    }

    private function createOrUpdateQuestions(
        Assessment $assessment,
        $questions
    ){
        foreach ($questions as $question) {
            $questionId = $question->id;
            $this->questionGrade = max(collect($question->columns)->pluck('grade')->toArray()) * $question->no_of_rows;
            $questionData = [
                'question' => $question->question ?? null,
                'number_of_columns'=> $question->no_of_columns,
                'number_of_rows'=>$question->no_of_rows
            ];
            $skipQuestion = isset($question->skip_question) ? $question->skip_question:false;
            if (Str::contains($questionId, 'new')) {
                $questionObj = $this->matrixRepository->createQuestion($questionData);
                $assessmentQuestion = $this->updateOrCreateAssessmentQuestions($assessment, $questionObj,$skipQuestion);
                if ($skipQuestion) {
                    $assessment->skipped_questions_grades = $assessment->skipped_questions_grades + $this->questionGrade;
                } else {
                    $assessment->mark = $assessment->mark + $this->questionGrade;
                }

                $assessment->save();
            } else {
                $questionObj = $this->matrixRepository->updateQuestion($questionId, $questionData);
                $prevAssessmentQuestion = $questionObj->assessmentQuestion;
                $assessmentQuestion = $this->updateOrCreateAssessmentQuestions($assessment,$questionObj, $skipQuestion);
                if ($prevAssessmentQuestion->skip_question) {
                    $assessment->skipped_questions_grades = $assessment->skipped_questions_grades - $prevAssessmentQuestion->question_grade;
                } else {
                    $assessment->mark = $assessment->mark - $prevAssessmentQuestion->question_grade;
                }

                if ($skipQuestion) {
                    $assessment->skipped_questions_grades = $assessment->skipped_questions_grades + $this->questionGrade;
                } else {
                    $assessment->mark = $assessment->mark + $this->questionGrade;
                }
                $assessment->save();
            }


            $this->createOrUpdateColumns($this->matrixRepository, $questionObj->id, $question);
            $this->createOrUpdateRows($this->matrixRepository, $questionObj->id, $question);
        }
        return $assessmentQuestion;
    }

    private function createOrUpdateColumns(MatrixQuestionRepository $matrixQuestionRepo, $questionId, $question)
    {
        $columns = $question->columns ?? [];

        $columnsDataMultiple = [];
        $this->deleteColumns($matrixQuestionRepo, $questionId, $columns);

        foreach ($columns as $column) {

            $columnId = $column->id;
            $columnData = [
                'text' => $column->text,
                'assess_data_id' => $questionId,
                'grade'=>$column->grade
            ];

            if (Str::contains($columnId, 'new')) {
                $columnsDataMultiple[] = $columnData;
            } else {
                $matrixQuestionRepo->updateColumns($columnId, $columnData);
            }
        }

        if (count($columnsDataMultiple) > 0) {
            $insert = $matrixQuestionRepo->insertMultipleColumns($columnsDataMultiple);
        }
    }

    private function deleteColumns(MatrixQuestionRepository $matrixQuestionRepo, $questionId, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldQuestionsIds = $matrixQuestionRepo->getQuestionColumnsIds($questionId);

        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $matrixQuestionRepo->deleteColumns($questionId, $deleteIds);
    }


    private function createOrUpdateRows(MatrixQuestionRepository $matrixQuestionRepo, $questionId, $question)
    {
        $rows = $question->rows ?? [];

        $rowsDataMultiple = [];
        $this->deleteRows($matrixQuestionRepo, $questionId, $rows);

        foreach ($rows as $row) {

            $rowId = $row->id;
            $rowData = [
                'text' => $row->text,
                'assess_data_id' => $questionId
            ];

            if (Str::contains($rowId, 'new')) {
                $rowsDataMultiple[] = $rowData;
            } else {
                $matrixQuestionRepo->updateRows($rowId, $rowData);
            }
        }

        if (count($rowsDataMultiple) > 0) {
            $insert = $matrixQuestionRepo->insertMultiplerows($rowsDataMultiple);
        }
    }

    private function deleteRows(MatrixQuestionRepository $matrixQuestionRepo, $questionId, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldQuestionsIds = $matrixQuestionRepo->getQuestionRowsIds($questionId);

        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $matrixQuestionRepo->deleteRows($questionId, $deleteIds);
    }

    private function updateOrCreateAssessmentQuestions(Assessment $assessment, AssessmentMatrixData $question, bool $skipQuestion)
    {
        $data = [
            'question_type' => AssessmentMatrixData::class,
            'question_id' => $question->id,
            'school_account_branch_id' => auth()->user()->branch->id ?? null,
            'school_account_id' => auth()->user()->branch->schoolAccount->id ?? null,
            'assessment_id'=>$assessment->id,
            'question_grade'=>$this->questionGrade,
            'skip_question' => $skipQuestion,
            'category_id'=> $this->category_id
        ];

        if ($this->matrixType) {
            $data['slug'] = $this->matrixType;
        }

        $question =  $this->assessmentQuestionRepo->updateOrCreate($assessment->id,$question->id,$data);
        return $question;
    }
}
