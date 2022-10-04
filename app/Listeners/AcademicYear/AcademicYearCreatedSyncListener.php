<?php

declare(strict_types = 1);

namespace App\Listeners\AcademicYear;

use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use Illuminate\Support\Facades\Log;

class AcademicYearCreatedSyncListener
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
                $academicYearId = $payload['ta3lom_reference'];
                $dataPrepared = [
                    'our_edu_reference' => $payload['our_edu_reference']
                ];
                $option = $this->repository->find($academicYearId);
                $this->repository->update($option, $dataPrepared);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
