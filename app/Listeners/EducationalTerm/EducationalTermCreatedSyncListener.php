<?php

declare(strict_types = 1);

namespace App\Listeners\EducationalTerm;

use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use Illuminate\Support\Facades\Log;

class EducationalTermCreatedSyncListener
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

    public function handle($payload)
    {
        try {
            if (!empty($payload)) {
                $dataPrepared = [
                    'our_edu_reference' => $payload['our_edu_reference']
                ];
                $optionId = $payload['ta3lom_reference'];
                $option = $this->repository->find($optionId);
                $this->repository->update($option, $dataPrepared);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
