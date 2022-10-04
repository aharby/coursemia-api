<?php


namespace App\OurEdu\LookUp\Transformers;


use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class AllowedQuestionsCountLookUpTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [
    ];

    private $param;

    public function __construct(array $param = [])
    {
        $this->param = $param;
    }

    /**
     * @return array
     */
    public function transform(int $allowedQuestionsCount)
    {
        return [
            'id' => $allowedQuestionsCount,
            'name' => $allowedQuestionsCount,
        ];
    }
}
