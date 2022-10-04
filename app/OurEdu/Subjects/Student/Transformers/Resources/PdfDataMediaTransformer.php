<?php


namespace App\OurEdu\Subjects\Student\Transformers\Resources;

use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;
use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfDataMedia;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class PdfDataMediaTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

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
     * @param PdfData $pdfDataMedia
     * @return array
     */
    public function transform(PdfDataMedia $pdfDataMedia)
    {
        return [
            'id' => Str::uuid(),
            'url' => resourceMediaUrl($pdfDataMedia->filename),
            'filename' => $pdfDataMedia->filename,
            'mime_type' => (string) $pdfDataMedia->mime_type,

        ];
    }


}

