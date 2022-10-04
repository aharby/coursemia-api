<?php

declare(strict_types=1);

namespace App\Listeners\Subject;

use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\GradeClasses\Repository\GradeClassRepositoryInterface;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\Producers\AcademicYear\AcademicYearCreatedSync;
use App\Producers\EducationalSystem\EducationalSystemCreatedSync;
use App\Producers\EducationalTerm\EducationalTermCreatedSync;
use App\Producers\GradeClass\GradeCreatedSync;
use Illuminate\Support\Facades\Log;

class SubjectPayloadHandler
{
    public function __construct(
        SubjectRepositoryInterface $subjectRepository,
        GradeClassRepositoryInterface $gradeClassRepository,
        OptionRepositoryInterface $optionRepository,
        EducationalSystemRepositoryInterface $educationalSystemRepository
    ) {
        $this->repository = $subjectRepository;
        $this->educationalSystemRepository = $educationalSystemRepository;
        $this->gradeClassRepository = $gradeClassRepository;
        $this->optionRepository = $optionRepository;
    }

    public function handle(array $payload): array
    {

        $educationalSystemId = $this->educationalSystemId($payload['educational_system']);
        $dataPrepared = [
            'educational_system_id' => $educationalSystemId,
            'grade_class_id' => $this->gradeId($payload['grade'], $educationalSystemId),
            'academic_year_id' => $this->academicYearId($payload['academic_year']),
            'educational_term_id' => $this->educationalTermId($payload['semester']),
            'is_active' => 1
        ];
        return $dataPrepared;
    }

    public function gradeId(array $gradeData, $educationalSystemId)
    {
        $gradeId = $gradeData['ta3lom_reference'];
        if (is_null($gradeId)) {
            $dataPrepared = [
                'title:en' => $gradeData['name_en'],
                'title:ar' => $gradeData['name_ar'],
                'our_edu_reference' => $gradeData['our_edu_reference'],
                'educational_system_id' => $educationalSystemId,
                'country_id' => env('fall_back_country_id'),
                'is_active' => 1
            ];

            // in case our_edu_reference exists but ta3lom_reference not updated in ouredu
            $grade = GradeClass::where('our_edu_reference', $gradeData['our_edu_reference'])->first();
            if (!$grade) {
                $grade =  $this->gradeClassRepository->create($dataPrepared);
            }
            GradeCreatedSync::publish([
                'our_edu_reference' => $grade->our_edu_reference,
                'ta3lom_reference' => $grade->id,
            ]);
            return $grade->id;
        }
        return $gradeId;
    }


    public function educationalSystemId(array $educationalSystemData)
    {
        $educationalSystemId = $educationalSystemData['ta3lom_reference'];
        if (is_null($educationalSystemId)) {
            $dataPrepared = [
                'name:en' => $educationalSystemData['name_en'],
                'name:ar' => $educationalSystemData['name_ar'],
                'our_edu_reference' => $educationalSystemData['our_edu_reference'],
                'is_active' => 1
            ];

            // in case our_edu_reference exists but ta3lom_reference not updated in ouredu
            $educationalSystem = EducationalSystem::where('our_edu_reference', $educationalSystemData['our_edu_reference'])->first();
            if (!$educationalSystem) {
                $educationalSystem =  $this->educationalSystemRepository->create($dataPrepared);
            }

            EducationalSystemCreatedSync::publish([
                'our_edu_reference' => $educationalSystem->our_edu_reference,
                'ta3lom_reference' => $educationalSystem->id,
            ]);

            return $educationalSystem->id;
        }
        return $educationalSystemId;
    }


    public function academicYearId(array $academicYearData)
    {
        $academicYearId = $academicYearData['ta3lom_reference'];
        if (is_null($academicYearId)) {
            $dataPrepared = [
                'title:en' => $academicYearData['name_en'],
                'title:ar' => $academicYearData['name_ar'],
                'our_edu_reference' => $academicYearData['our_edu_reference'],
                'type'  =>  OptionsTypes::ACADEMIC_YEAR,
                'is_active' => 1
            ];
            $academicYear = Option::whereType(OptionsTypes::ACADEMIC_YEAR)
                ->where('our_edu_reference', $academicYearData['our_edu_reference'])->first();
            if (!$academicYear) {
                $academicYear = $this->optionRepository->create($dataPrepared);
            }

            AcademicYearCreatedSync::publish([
                'our_edu_reference' => $academicYear->our_edu_reference,
                'ta3lom_reference' => $academicYear->id,
            ]);

            return $academicYear->id;
        }
        return $academicYearId;
    }

    public function educationalTermId(array $educationalTermData)
    {
        $educationalTermId = $educationalTermData['ta3lom_reference'];
        if (is_null($educationalTermId)) {
            $dataPrepared = [
                'title:en' => $educationalTermData['name_en'],
                'title:ar' => $educationalTermData['name_ar'],
                'our_edu_reference' => $educationalTermData['our_edu_reference']
            ];
            // in case our_edu_reference exists but ta3lom_reference not updated in ouredu
            $educationalTerm = Option::whereType(OptionsTypes::EDUCATIONAL_TERM)->where('our_edu_reference', $educationalTermData['our_edu_reference'])->first();

            if (!$educationalTerm) {
                $educationalTerm =  Option::create(array_merge(['is_active' => 1, 'type' => OptionsTypes::EDUCATIONAL_TERM], $dataPrepared));
            }

            EducationalTermCreatedSync::publish([
                'our_edu_reference' => $educationalTerm->our_edu_reference,
                'ta3lom_reference' => $educationalTerm->id,
            ]);

            return $educationalTerm->id;
        }
        return $educationalTermId;
    }
}
