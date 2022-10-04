<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\Pdf;

use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;

interface PdfRepositoryInterface
{
    /**
     * @param array $data
     * @return PdfData|null
     */
    public function create(array $data): ?PdfData;


    /**
     * @param int $id
     * @return PdfData|null
     */
    public function findOrFail(int $id): ?PdfData;

    /**
     * @param PdfData $pdfData
     * @param array $data
     * @return bool
     */
    public function update(PdfData $pdfData, array $data): ?PdfData;

    public function media();


    public function getPdfDateBySubjectFormatId($resourceSubjectFormatSubjectId): ?PdfData;
}
