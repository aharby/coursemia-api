<?php

declare(strict_types = 1);

namespace App\Listeners\EducationalTerm;

use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use App\Producers\EducationalTerm\EducationalTermCreatedSync;
use Illuminate\Support\Facades\Log;

class CreateEducationalTermListener
{
    private $repository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(OptionRepositoryInterface $optionRepository)
    {
        $this->repository = $optionRepository;
    }

    public function handle(array $payload)
    {
        try {
            if (!empty($payload)) {
                $dataPrepared = [
                    'title:en' => $payload['name_en'],
                    'title:ar' => $payload['name_ar'],
                    'our_edu_reference' => $payload['our_edu_reference'],
                    'type' => OptionsTypes::EDUCATIONAL_TERM
                ];
                $createdEducationalTerm = $this->repository->create($dataPrepared);
                EducationalTermCreatedSync::publish(
                    [
                        'our_edu_reference' => $createdEducationalTerm->our_edu_reference,
                        'ta3lom_reference' => $createdEducationalTerm->id,
                    ]
                );
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
