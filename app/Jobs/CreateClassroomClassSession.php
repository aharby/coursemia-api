<?php

namespace App\Jobs;

use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClass\ImportJob;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Enums\ImportJobsStatusEnums;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Events\ClassroomClassCreationEvent;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateClassroomClassSession implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;


    /**
     * @var Classroom
     */
    private $classroom;
    /**
     * @var array
     */
    private $data;
    /**
     * @var ImportJob|null
     */
    private $importJob;
    /**
     * @var int|null
     */
    private $row;
    /**
     * @var bool
     */
    private $isLastRow;


    /**
     * Create a new job instance.
     *
     * @param Classroom $classroom
     * @param array $data
     * @param ImportJob|null $importJob
     * @param int|null $row
     * @param bool|null $isLastRow
     */
    public function __construct(Classroom $classroom, array $data, ImportJob $importJob = null, int $row = null, bool $isLastRow = null)
    {
        $this->classroom = $classroom;
        $this->data = $data;
        $this->importJob = $importJob;
        $this->row = $row;
        $this->isLastRow = ($isLastRow==true);
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Illuminate\Contracts\Redis\LimiterTimeoutException
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            $classroomClass = $this->classroom->classroomClass()->create($this->data);
            $classroomClass->createOrUpdateSessions();

            if (!isset($this->importJob)) {
                event(new ClassroomClassCreationEvent($this->data["channelId"], true));
            }
            DB::commit();
        } catch (ValidationException $exception) {
            DB::rollBack();
            $this->addError($exception->errors());
        } catch (Exception $exception) {
            DB::rollBack();
            $this->addError($exception->getMessage());
        }
        if (isset($this->importJob) and $this->isLastRow) {
            $this->importJob->update(['status' => ImportJobsStatusEnums::COMPLETED]);
        }
    }

    private function addError($errors)
    {
        $errorsMessages = [];

        if (isset($this->importJob)) {
            $this->importJob->increment("has_errors");
        }

        if (is_array($errors)) {
            foreach ($errors as $error) {
                if (is_array($error)) {
                    foreach ($error as $message) {
                        if (isset($this->importJob)) {
                            $this->importJob->errors()->create(
                                [
                                    'error' => $message,
                                    'row' => $this->row
                                ]
                            );
                        } else {
                            $errorsMessages[] = $message;
                        }
                    }
                } else {
                    if (isset($this->importJob)) {
                        $this->importJob->errors()->create(
                            [
                                'error' => $error,
                                'row' => $this->row
                            ]
                        );
                    } else {
                        $errorsMessages[] = $error;
                    }
                }
            }
        } else {
            if (isset($this->importJob)) {
                $this->importJob->errors()->create(
                    [
                        'error' => $errors,
                        'row' => $this->row
                    ]
                );
            } else {
                $errorsMessages[] = $errors;
            }
        }
        if (!isset($this->importJob)) {
            event(new ClassroomClassCreationEvent($this->data["channelId"], false, $errorsMessages));
        }
    }
}
