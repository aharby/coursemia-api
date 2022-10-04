<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\Pdf;

use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;

class PdfRepository implements PdfRepositoryInterface
{

    private $pdfData;

    public function __construct(PdfData $pdfData)
    {
        $this->pdfData = $pdfData;
    }

    /**
     * @param array $data
     * @return PdfData|null
     */
    public function create(array $data): ?PdfData
    {
        return $this->pdfData->create($data);
    }

    /**
     * @param int $id
     * @return PdfData|null
     */
    public function findOrFail(int $id): ?PdfData
    {
        return $this->pdfData->findOrFail($id);
    }

    /**
     * @param PdfData $pdfData
     * @param array $data
     * @return bool
     */
    public function update(PdfData $pdfData, array $data): ?PdfData
    {
         $pdfData->update($data);
        return $this->pdfData->findOrFail($pdfData->id);

    }

    public function media()
    {
        return $this->pdfData->media();
    }


    public function getPdfDateBySubjectFormatId($resourceSubjectFormatSubjectId): ?PdfData
    {
        return $this->pdfData->where('resource_subject_format_subject_id', $resourceSubjectFormatSubjectId)->first();
    }

}
