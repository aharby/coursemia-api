<?php


namespace App\OurEdu\Subjects\Student\Transformers;

use App\OurEdu\Reports\ReportEnum;
use App\OurEdu\ResourceSubjectFormats\Models\Progress\ResourceProgressStudent;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\Page\PageData;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;
use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureData;
use App\OurEdu\Subjects\Student\Transformers\Resources\PdfTransformer;
use App\OurEdu\Subjects\Student\Transformers\Resources\PageTransformer;
use App\OurEdu\Subjects\Student\Transformers\Resources\AudioTransformer;
use App\OurEdu\Subjects\Student\Transformers\Resources\FlashTransformer;
use App\OurEdu\Subjects\Student\Transformers\Resources\VideoTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Student\Transformers\Resources\PictureTransformer;

class ResourceSubjectFormatSubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
        'details',
        'breadcrumbs'
    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }
    /**
     * @param ResourceSubjectFormatSubject $resourceSubjectFormatSubject
     * @return array
     */
    public function transform(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        $student = auth()->user();
        $progress = calculateResourceProgress( $resourceSubjectFormatSubject , $student);

        return [
            'id' => (int)$resourceSubjectFormatSubject->id,
            'resource_slug' => (string)$resourceSubjectFormatSubject->resource_slug,
            'progress' => $progress
        ];
    }

    public function includeDetails(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        switch ($resourceSubjectFormatSubject->resource_slug) {
            case LearningResourcesEnums::PAGE:
                $pageData = PageData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();

                if ($pageData) {
                    return $this->item(
                        $pageData,
                        new PageTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }

                break;
            case LearningResourcesEnums::Audio:
                $audioData = AudioData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();

                if ($audioData) {
                    return $this->item(
                        $audioData,
                        new AudioTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;
            case LearningResourcesEnums::PDF:
                $pdfData = PdfData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();
                if ($pdfData) {
                    return $this->item(
                        $pdfData,
                        new PdfTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;
            case LearningResourcesEnums::PICTURE:
                $pictureData = PictureData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();
                if ($pictureData) {
                    return $this->item(
                        $pictureData,
                        new PictureTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;

            case LearningResourcesEnums::FLASH:
                $flashData = FlashData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();
                if ($flashData) {
                    return $this->item(
                        $flashData,
                        new FlashTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;

            case LearningResourcesEnums::Video:
                $videoData = VideoData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();
                if ($videoData) {
                    return $this->item(
                        $videoData,
                        new VideoTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;
        }
    }

    public function includeActions($resourceSubjectFormatSubject)
    {
        $actions = [];

        $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.student.report.post.create', ['subjectId' => $resourceSubjectFormatSubject->subjectFormatSubject->subject_id,'reportType' => ReportEnum::RESOURCE_TYPE,'id'=>$resourceSubjectFormatSubject->id]),
                    'label' => trans('subject.Report'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::REPORT_RESOURCE
                ];

        if (!isset($this->params['details'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.subjects.viewResourceSubjectFormatSubjectDetails', ['sectionId' => $resourceSubjectFormatSubject->subjectFormatSubject->id, 'resourceID' => $resourceSubjectFormatSubject->id]),
                'label' => trans('subject.view resource details'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_RESOURCE_SUBJECT_DETAILS
            ];
        }

        return $this->collection(
            $actions,
            new ActionTransformer(),
            ResourceTypesEnums::ACTION
        );
    }


    public function includeBreadcrumbs($resourceSubjectFormatSubject)
    {
        $subjectFormatSubject = $resourceSubjectFormatSubject->subjectFormatSubject()->first();

        $parentSctionsIds = getBreadcrumbsIds($subjectFormatSubject,[]);

        return $this->collection(
            $parentSctionsIds,
            new BreadcrumbsTransformer(),
            ResourceTypesEnums::BREADCRUMB
        );
    }


}
