<?php

namespace App\OurEdu\PsychologicalTests\Transformers;

use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalResult;

class PsychologicalResultTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'recomendation',
        'test',
    ];

    protected $user;

    public function __construct($user = null)
    {
        $this->user = $user ?? new User;
    }

    public function transform(PsychologicalResult $result)
    {
        $transformedData = [
            'id' => (int) $result->id,
            'percentage' => (string) $result->percentage,
        ];

        return $transformedData;
    }

    public function includeRecomendation(PsychologicalResult $result)
    {
        if ($result->recomendation()->exists()) {
            return $this->item($result->recomendation, new PsychologicalRecomendationTransformer(), ResourceTypesEnums::PSYCHOLOGICAL_RECOMENDATION);
        }
    }

    public function includeTest(PsychologicalResult $result)
    {
        if ($result->test()->exists()) {
            return $this->item($result->test, new PsychologicalTestTransformer(), ResourceTypesEnums::PSYCHOLOGICAL_TEST);
        }
    }
}
