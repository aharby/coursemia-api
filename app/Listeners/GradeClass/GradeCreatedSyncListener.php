<?php

declare(strict_types = 1);

namespace App\Listeners\GradeClass;

use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\GradeClasses\Repository\GradeClassRepositoryInterface;
use Illuminate\Support\Facades\Log;

class GradeCreatedSyncListener
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

    public function handle($payload)
    {
        try {
            if (!empty($payload)) {
                $gradeId = $payload['ta3lom_reference'];
                $dataPrepared = [
                    'our_edu_reference' => $payload['our_edu_reference'],
                ];
                $gradeClass = $this->repository->findOrFail($gradeId);
                $this->repository->update($gradeClass, $dataPrepared);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
