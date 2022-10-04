<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources\Resources;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Transformers\PdfDataMediaTransformer;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Subjects\UseCases\SubjectStructural\GenerateTasksUseCase;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCaseInterface;
use League\Fractal\TransformerAbstract;

class PdfTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'pdfDataMedia'
    ];
    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param PdfData $pdfData
     * @return array
     */
    public function transform(PdfData $pdfData)
    {
        return [
            'id' => $pdfData->id,
            'title' => $pdfData->title,
            'description' => $pdfData->description,
            'pdf' => $pdfData->pdf_type == 'link' ? $pdfData->link : null,
            'pdf_type' => $pdfData->pdf_type

        ];
    }

    public function includePdfDataMedia(PdfData $pdfData)
    {
        if ($pdfData->media()->exists()) {
            return $this->item($pdfData->media()->first(), new PdfDataMediaTransformer(), ResourceTypesEnums::RESOURCE_DATA_MEDIA);
        }
    }
}
