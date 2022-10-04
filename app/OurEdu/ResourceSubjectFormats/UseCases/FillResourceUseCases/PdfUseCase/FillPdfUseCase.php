<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PdfUseCase;

use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;
use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfDataMedia;
use App\OurEdu\ResourceSubjectFormats\Repository\Pdf\PdfRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Support\Str;

class FillPdfUseCase implements FillPdfUseCaseInterface
{
    private $pdfRepository;
    private $pdfData;

    public function __construct(PdfRepositoryInterface $pdfRepository, PdfData $pdfData)
    {
        $this->pdfRepository = $pdfRepository;
        $this->pdfData = $pdfData;
    }

    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     * @return mixed|void
     */
    public function fillResource(int $resourceSubjectFormatId, $data, User $user)
    {

        $resourceSubjectFormatSubjectData = $data->resource_subject_format_subject_data;

        if (isset($resourceSubjectFormatSubjectData->pdf) && !empty($resourceSubjectFormatSubjectData->pdf)) {
            $pdf_type = 'link';
            $link = $resourceSubjectFormatSubjectData->pdf;
            $pdfData = [
                'title' => $resourceSubjectFormatSubjectData->title,
                'description' => $resourceSubjectFormatSubjectData->description,
                'resource_subject_format_subject_id' => $resourceSubjectFormatId,
                'pdf_type' => $pdf_type,
                'link' => $link
            ];
        } else {
            $pdf_type = 'pdf';
            $pdfData = [
                'title' => $resourceSubjectFormatSubjectData->title,
                'description' => $resourceSubjectFormatSubjectData->description,
                'resource_subject_format_subject_id' => $resourceSubjectFormatId,
                'pdf_type' => $pdf_type,
            ];
        }

        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();
        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {
            $returnPdfData = $this->pdfRepository->getPdfDateBySubjectFormatId($resourceSubjectFormatId);
            if ($returnPdfData) {

                $pdfObj = $this->pdfRepository->update($returnPdfData, $pdfData);
            } else {
                $pdfObj = $this->pdfRepository->create($pdfData);

            }

            $oldIds = $pdfObj->media()->pluck('id')->toArray();

            if ($data->attach_media) {

                moveGarbageMedia($data->attach_media->getId(), $pdfObj->media(), 'subject/pdfs');

                //To Remove Old & duplication
                deleteMedia($oldIds, $pdfObj->media());
            }

            if ($data->detach_media) {
                deleteMedia($data->detach_media->getId(), new PdfDataMedia());
            }
        } else {
            $update = $this->pdfRepository->findOrFail($resourceSubjectFormatSubjectDataId);
            $oldIds = $update->media()->pluck('id')->toArray();

            if ($data->detach_media) {
                deleteMedia($data->detach_media->getId(), new PdfDataMedia());
            }
            if ($data->attach_media) {

                moveGarbageMedia($resourceSubjectFormatSubjectData->attach_media->getId(), $update->media(), 'subject/pdfs');

                //To Remove Old & duplication
                deleteMedia($oldIds, $update->media());

            }
            $this->pdfRepository->update($update, $pdfData);
        }
    }
}
