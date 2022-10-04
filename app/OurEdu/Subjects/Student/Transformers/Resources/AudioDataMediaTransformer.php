<?php


namespace App\OurEdu\Subjects\Student\Transformers\Resources;

use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioDataMedia;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class AudioDataMediaTransformer extends TransformerAbstract
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

    /**
     * @param AudioData $audioDataMedia
     * @return array
     */
    public function transform(AudioDataMedia $audioDataMedia)
    {
        return [
            'id' => Str::uuid(),
            'url' => resourceMediaUrl($audioDataMedia->filename),
            'filename' => $audioDataMedia->filename,
            'extension' => $audioDataMedia->extension,
            'mime_type' => $audioDataMedia->mime_type
        ];
    }
}
