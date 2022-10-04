<?php


namespace App\OurEdu\ResourceSubjectFormats\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;
use League\Fractal\TransformerAbstract;

class PdfDataTransformer extends TransformerAbstract
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

    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
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
            return $this->item($pdfData->media()->first(),new PdfDataMediaTransformer(),ResourceTypesEnums::RESOURCE_DATA_MEDIA);
        }
    }


}

