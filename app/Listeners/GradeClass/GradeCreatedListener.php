<?php

declare(strict_types=1);

namespace App\Listeners\GradeClass;

use App\OurEdu\GradeClasses\Repository\GradeClassRepositoryInterface;
use App\Producers\GradeClass\GradeCreatedSync;
use Illuminate\Support\Facades\Log;

class GradeCreatedListener
{
    private $repository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(GradeClassRepositoryInterface $gradeRepository)
    {
        $this->repository = $gradeRepository;
    }

    public function handle(array $payload)
    {
        try {
            if (!empty($payload)) {
                $dataPrepared = [
                    'title:en' => $payload['name_en'],
                    'title:ar' => $payload['name_ar'],
                    'our_edu_reference' => $payload['our_edu_reference'],
                    'country_id' => env('fall_back_country_id')
                ];
                $createdGrade = $this->repository->create($dataPrepared);
                GradeCreatedSync::publish([
                    'ta3lom_reference' => $createdGrade->id,
                    'our_edu_reference' => $createdGrade->our_edu_reference,
                ]);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
