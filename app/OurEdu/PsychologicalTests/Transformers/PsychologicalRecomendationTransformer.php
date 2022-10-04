<?php

namespace App\OurEdu\PsychologicalTests\Transformers;

use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalRecomendation;

class PsychologicalRecomendationTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
    ];

    protected $user;

    public function __construct($user = null)
    {
        $this->user = $user ?? new User;
    }

    public function transform(PsychologicalRecomendation $recomendation)
    {
        $transformedData = [
            'id' => (int) $recomendation->id,
            'result' => (string) $recomendation->result,
            'recomendation' => (string) $recomendation->recomendation,
            'is_active' => (boolean) $recomendation->is_active,
        ];

        return $transformedData;
    }
}
