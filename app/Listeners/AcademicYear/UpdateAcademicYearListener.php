<?php

declare(strict_types = 1);

namespace App\Listeners\AcademicYear;

use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use Illuminate\Support\Facades\Log;

class UpdateAcademicYearListener
{
    private $repository;

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
