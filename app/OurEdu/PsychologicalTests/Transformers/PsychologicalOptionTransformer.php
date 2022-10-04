<?php

namespace App\OurEdu\PsychologicalTests\Transformers;

use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalOption;

class PsychologicalOptionTransformer extends TransformerAbstract
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

    public function transform(PsychologicalOption $option)
    {
        $transformedData = [
            'id' => (int) $option->id,
            'name' => (string) $option->name,
            'is_active' => (boolean) $option->is_active,
        ];

        return $transformedData;
    }
}
