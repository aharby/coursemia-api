<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources\Resources;

use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioDataMedia;
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
            'id' => $audioDataMedia->id,
            'url' => resourceMediaUrl($audioDataMedia->filename),
            'filename' => $audioDataMedia->filename,
        ];
    }
}
