<?php

declare(strict_types=1);

namespace App\Listeners\AcademicYear;

use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use App\Producers\AcademicYear\AcademicYearCreatedSync;
use Illuminate\Support\Facades\Log;

class CreateAcademicYearListener
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
                    'type'  =>  OptionsTypes::ACADEMIC_YEAR
                ];
                $createdAcademicYear = $this->repository->create($dataPrepared);
                AcademicYearCreatedSync::publish([
                    'our_edu_reference' => $createdAcademicYear->our_edu_reference,
                    'ta3lom_reference' => $createdAcademicYear->id,
                ]);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
