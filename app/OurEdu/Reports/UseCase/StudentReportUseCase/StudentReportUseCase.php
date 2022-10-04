<?php

namespace App\OurEdu\Reports\UseCase\StudentReportUseCase;

use App\OurEdu\Reports\Report;
use App\OurEdu\Reports\Repository\ReportSubjectFormatSubjectRepositoryInterface;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Helpers\MailManger;
use App\OurEdu\Reports\ReportEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Reports\Repository\ReportRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;

class StudentReportUseCase implements StudentReportUseCaseInterface
{
    private $reportRepository;
    private $subjectRepository;
    private $reportSubjectFormatSubjectRepository;

    public function __construct(ReportRepositoryInterface $reportRepository, SubjectRepositoryInterface $userRepository , ReportSubjectFormatSubjectRepositoryInterface $reportSubjectFormatSubjectRepository)
    {
        $this->reportRepository = $reportRepository;
        $this->subjectRepository = $userRepository;
        $this->reportSubjectFormatSubjectRepository = $reportSubjectFormatSubjectRepository;
        $this->user = Auth::guard('api')->user();
    }

    /**
     * @param array $data
     * @return array
     */
    public function studentReport(array $data): array
    {
        $returnArr = [];
        // Check Supported Report Type
        if (is_null(ReportEnum::getType($data['reportable_type']))) {
            $returnArr['code'] = 422;
            $returnArr['title'] = 'Report Type';
            $returnArr['detail'] = trans('report.This type not supported to report');
            return $returnArr;
        }

        $createReportArr = [
            'report' => $data['report'],
            'reportable_id' => $data['reportable_id'],
            'reportable_type' => ReportEnum::getType($data['reportable_type']),
            'student_id' => $data['student_id'],
        ];
        $report = $this->reportRepository->create($createReportArr);


        if ($report instanceof Report) {
            //Draw Reports hierarchy
            if ($report->reportable_type == ReportEnum::SUBJECT_FORMAT_SUBJECT_MODEL) {
                $section = $report->reportable;
                $this->drawHierarchy($section->id , $section->subject_id);
            }else if ($report->reportable_type == ReportEnum::RESOURCE_SUBJECT_FORMAT_SUBJECT_MODEL) {
                $section = $report->reportable->subjectFormatSubject;
                $this->drawHierarchy($section->id , $section->subject_id);
            }
            // Find Subject
            $subject = $this->subjectRepository->findOrFail($data['subjectId']);
            // Get Sme of Subject
            $sme = new SubjectRepository($subject);
            $sme = $sme->getSme();

            if ($sme) {
                // Handle Email
                $newMail = new MailManger();
                $emailData = [
                    'user_type' => UserEnums::STUDENT_TYPE,
                    'data' => ['url' => 'To Do URL of Report', 'report' => $report->report],
                    'subject' => trans('emails.New Report Subject'),
                    'emails' => [$sme->email],
                    'view' => 'studentReport'
                ];
                $newMail->prepareMail($emailData);
                $newMail->handle();
                // Return Email
            }

            Cache::remember("{$this->user->id}_reported", now()->addMinutes(env('REPORT_CACHE_TIME', 1)), function () {
                return 1;
            });

            $returnArr['code'] = 200;
            $returnArr['meta'] = trans('report.Report Created Successfully');
            $returnArr['report'] = $report;
            return $returnArr;
        } else {
            // SomeThing Error
            $returnArr['code'] = 500;
            $returnArr['title'] = 'Oopps Something is broken';
            $returnArr['detail'] = trans('app.Oopps Something is broken');
            return $returnArr;
        }
    }

    public function drawHierarchy($sectionId , $subjectID) {

        $parentSection = getSectionParent($sectionId);
        $childId = $sectionId;
        while ($parentSection) {

            $this->reportSubjectFormatSubjectRepository->firstOrCreate([
                'section_id' => $childId,
                'section_parent_id' => $parentSection->id,
                'subject_id' => $subjectID,
            ]);

            $childId = $parentSection->id;
            $parentSection =  getSectionParent($childId);
        }

        $this->reportSubjectFormatSubjectRepository->firstOrCreate([
            'section_id' => $childId,
            'section_parent_id' => null,
            'subject_id' => $subjectID,
        ]);
    }
}
