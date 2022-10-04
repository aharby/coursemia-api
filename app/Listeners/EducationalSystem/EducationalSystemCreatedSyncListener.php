<?php

declare(strict_types=1);

namespace App\Listeners\EducationalSystem;


use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use Illuminate\Support\Facades\Log;

class EducationalSystemCreatedSyncListener
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

    public function handle($payload)
    {
        try {
            if (!empty($payload)) {
                $educationSystemId = $payload['ta3lom_reference'];
                $dataPrepared = [
                    'our_edu_reference' => $payload['our_edu_reference']
                ];

                $this->repository->update($educationSystemId,$dataPrepared);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
