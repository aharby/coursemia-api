<?php

declare(strict_types=1);

namespace App\Listeners\EducationalSystem;

use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use Illuminate\Support\Facades\Log;

class UpdateEducationalSystemListener
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
                ];
                $this->repository->update($payload['ta3lom_reference'],$dataPrepared);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
