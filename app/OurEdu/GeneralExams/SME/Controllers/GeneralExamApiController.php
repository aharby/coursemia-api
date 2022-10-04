<?php

namespace App\OurEdu\GeneralExams\SME\Controllers;

use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepository;
use App\OurEdu\GeneralExams\SME\Transformers\ListGeneralExamStudentsTransformer;
use App\OurEdu\Options\Option;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\GeneralExams\SME\Requests\GeneralExamRequest;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\GeneralExams\SME\Transformers\SubjectTransformer;
use App\OurEdu\GeneralExams\SME\Transformers\GeneralExamTransformer;
use App\OurEdu\GeneralExams\SME\Requests\GeneralExamQuestionsRequest;
use App\OurEdu\GeneralExams\SME\Transformers\ListGeneralExamTransformer;
use App\OurEdu\GeneralExams\SME\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\GeneralExams\UseCases\GeneralExam\GeneralExamUseCaseInterface;
use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepositoryInterface;
use App\OurEdu\GeneralExams\SME\Transformers\PreparedGeneralExamQuestionTransformer;
use App\OurEdu\GeneralExams\Repository\PreparedQuestion\PreparedGeneralExamQuestionRepositoryInterface;

class GeneralExamApiController extends BaseApiController
{
    protected $subjectRepository;
    protected $prepredQuestionRepository;
    protected $parserInterface;
    protected $generalExamRepository;
    protected $generalExamUseCase;
    protected $filters = [];

    public function __construct(SubjectRepositoryInterface $subjectRepository, PreparedGeneralExamQuestionRepositoryInterface $prepredQuestionRepository, ParserInterface $parserInterface, GeneralExamRepositoryInterface $generalExamRepository, GeneralExamUseCaseInterface $generalExamUseCase)
    {
        $this->subjectRepository = $subjectRepository;
        $this->prepredQuestionRepository = $prepredQuestionRepository;

        $this->middleware('auth:api');
        $this->middleware('type:sme');
        $this->parserInterface = $parserInterface;
        $this->generalExamRepository = $generalExamRepository;
        $this->generalExamUseCase = $generalExamUseCase;
        $this->user = Auth::guard('api')->user();
    }

    public function getSubjectSections(GeneralExam $exam ,Subject $subject)
    {
        $this->setFilters();

        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];

        return $this->transformDataModInclude($subject,'',
            new SubjectTransformer($exam,[]), ResourceTypesEnums::SUBJECT , $meta);
    }

    public function getSectionQuestions(GeneralExam $exam , SubjectFormatSubject $section)
    {
        $this->setFilters();

        $meta = [
                'filters' => formatFiltersForApi($this->filters)
            ];
        $questions = $this->prepredQuestionRepository->paginateSectionQuestions($section ,$exam->difficulty_level_id, $this->filters);

        return $this->transformDataModInclude($questions, 'subjectFormatSubject', new PreparedGeneralExamQuestionTransformer($exam,[]), ResourceTypesEnums::PREPARED_GENERAL_EXAM_QUESTION, $meta);
    }

    public function index()
    {
        $exams = $this->generalExamRepository->paginateSmeExams($this->user);

        return $this->transformDataModInclude($exams, ['actions'], new ListGeneralExamTransformer(), ResourceTypesEnums::GENERAL_EXAM);
    }

    public function view($examId)
    {
        $exam = $this->generalExamRepository->findOrFail($examId);

        return $this->transformDataModInclude($exam, ['actions', 'preparedQuestions', 'questions.options'], new GeneralExamTransformer(), ResourceTypesEnums::GENERAL_EXAM);
    }

    public function storeGeneralExam(GeneralExamRequest $request, Subject $subject)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $exam = $this->generalExamUseCase->create($data);

        $meta = [
                'message' => trans('api.General exam created')
            ];

        return $this->transformDataModInclude($exam, '', new GeneralExamTransformer(), ResourceTypesEnums::GENERAL_EXAM, $meta);
    }

    public function updateQuestions(GeneralExamQuestionsRequest $request, $examId)
    {
        $exam = $this->generalExamRepository->findOrFail($examId);

        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $this->generalExamUseCase->updateQuestions($exam, $data);

        $meta = [
                'message' => trans('api.Prepared general exam questions updated')
            ];

        return $this->transformDataModInclude($exam->fresh(), ['preparedQuestions', 'questions.options'], new GeneralExamTransformer(), ResourceTypesEnums::GENERAL_EXAM, $meta);
    }

    public function update(GeneralExamRequest $request, $examId)
    {
        $exam = $this->generalExamRepository->findOrFail($examId);

        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $this->generalExamUseCase->update($exam, $data);

        $meta = [
                'message' => trans('api.General exam updated')
            ];

        return $this->transformDataModInclude($exam, ['actions', 'preparedQuestions', 'questions.options'], new GeneralExamTransformer(), ResourceTypesEnums::GENERAL_EXAM, $meta);
    }

    public function delete($examId)
    {
        $exam = $this->generalExamRepository->findOrFail($examId);

        $this->generalExamUseCase->delete($exam);

        return response()->json([
            'meta'  =>  [
                'message'   =>  trans('api.Deleted Successfully')
            ]
        ]);
    }

    public function publish($examId)
    {
        $exam = $this->generalExamRepository->findOrFail($examId);

        $this->generalExamUseCase->publishGeneralExam($exam);

        $meta = [
                'message'   =>  trans('api.Published Successfully')
            ];

        return $this->transformDataModInclude($exam->fresh(), ['actions', 'preparedQuestions', 'questions.options'], new GeneralExamTransformer(), ResourceTypesEnums::GENERAL_EXAM, $meta);
    }

    protected function setFilters()
    {
        $options = Option::whereIn('type', [
            OptionsTypes::RESOURCE_DIFFICULTY_LEVEL,
        ])->get();

        $this->filters[] = [
            'name' => 'difficulty_level_id',
            'type' => 'select',
            'data' => $options->where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->pluck('title', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('difficulty_level_id'),
        ];
    }

    public function getGeneralExamStudents($examId)
    {
        $students = $this->generalExamUseCase->generalExamStudent($examId);
        return $this->transformDataModInclude($students,'', new ListGeneralExamStudentsTransformer(),
            ResourceTypesEnums::GENERAL_EXAM_STUDENT);
    }
}
