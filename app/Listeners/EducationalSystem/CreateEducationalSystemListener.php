<?php

declare(strict_types=1);

namespace App\Listeners\EducationalSystem;

use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use App\Producers\EducationalSystem\EducationalSystemCreatedSync;
use Illuminate\Support\Facades\Log;

class CreateEducationalSystemListener
{
    private $repository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(EducationalSystemRepositoryInterface $educationalSystemRepository)
    {
        $this->repository = $educationalSystemRepository;
    }

    public function handle(array $payload)
    {
        try {
            if (!empty($payload)) {
                $dataPrepared = [
                    'name:en' => $payload['name_en'],
                    'name:ar' => $payload['name_ar'],
                    'our_edu_reference' => $payload['our_edu_reference'],
                    'country_id' => env('fall_back_country_id')
                ];
                $createdEducationalSystem = $this->repository->create($dataPrepared);
                EducationalSystemCreatedSync::publish([
                    'our_edu_reference' => $createdEducationalSystem->our_edu_reference,
                    'ta3lom_reference' => $createdEducationalSystem->id,
                ]);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
