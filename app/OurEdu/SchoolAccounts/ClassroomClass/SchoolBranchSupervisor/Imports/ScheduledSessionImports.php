<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Imports;

use App\Jobs\CreateClassroomClassSession;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use App\OurEdu\SchoolAccounts\ClassroomClass\ImportJob;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Enums\ImportJobsStatusEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class ScheduledSessionImports implements ToCollection, WithHeadingRow
{
    /**
     * @var ImportJob
     */
    private $importJob;

    /**
     * @var int
     */
    private $numberOfRows;

    /**
     * ScheduledSessionImports constructor.
     * @param ImportJob $importJob
     */
    public function __construct(ImportJob $importJob)
    {
        $this->importJob = $importJob;
        $this->numberOfRows = 0;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $this->importJob->update(['status' => ImportJobsStatusEnums::IN_PROGRESS]);
        $this->numberOfRows = count($collection);
        $i = 1;
        foreach ($collection as $row) {
            $i++;
            $this->validateData($row->toArray(), $i);
        }
    }

    private function validateData(array $row, int $rowNum)
    {
        $data = $this->prepareData($row);

        $rules = $this->rules($data);

        $messages = [
            'start_date_time.after' => 'يجب أن يكون التاريخ على الأقل هو تاريخ اليوم ,ووقت البدء يجب أن يكون أكبر من الوقت الحالي على الأقل دقيقة واحدة',
            'until_date.before' => 'يجب الا يتعدى تاريخ التكرار 6 اشهر من تاريخ اليوم',
        ];

        $validate = Validator::make($data, $rules, $messages);

        if (!$data['tue']  &&
            !$data['sun']  &&
            !$data['mon']  &&
            !$data['wed']  &&
            !$data['thu']  &&
            !$data['fri']  &&
            !$data['sat']  &&
            $data['repeat'] == 3
        ) {
            $this->addError(trans('classroomClass.please select at least one day'), $rowNum);
        } else {
             $validate->fails() ? $this->addError($validate->errors()->messages(), $rowNum) : $this->addData($data, $rowNum);
        }
    }

    private function rules(array $data = null): array
    {
        $rules =  [
            'from_time' => 'required|before:to_time',
            'to_time' => 'required|after:from_time',
            'from' => 'required|date',
            "sun" => 'required|boolean',
            "mon" => 'required|boolean',
            "tue" => 'required|boolean',
            "wed" => 'required|boolean',
            "subject_id" => ['required','exists:subjects,id',function ($attribute, $value, $fail) {
                $branchEducationalSystemGradeClass = $this->importJob->classroom->branchEducationalSystemGradeClass;
                $branchEducationalSystem = $branchEducationalSystemGradeClass->branchEducationalSystem;

                $subject = Subject::where('id', $value)
                        ->where('educational_system_id', $branchEducationalSystem->educationalSystem->id)
                        ->where('grade_class_id', $branchEducationalSystemGradeClass->gradeClass->id)
                        ->where('academical_years_id', $branchEducationalSystem->academicYear->id)
                        ->where('educational_term_id', $branchEducationalSystem->educationalTerm->id)
                    ->first();

                if (!$subject) {
                    $fail(
                        trans(
                            'app.import_classes_subject_error',
                            [
                            'attribute' => $attribute,
                            'classroom' => ("<span style='color: green'>" . (  $this->importJob->classroom->name ?? '') ."</span>"),
                            ]
                        )
                    );
                }
            }],
            'subject_instructor_id' => [function ($attribute, $value, $fail) {
                if ($value != true) {
                    $fail(
                        trans(
                            'app.import_classes_subject_instructor_error',
                            [
                            'attribute' => $attribute
                            ]
                        )
                    );
                }
            }],
            'subject_instructor_branch' => [function ($attribute, $value, $fail) {
                if ($value != true) {
                    $fail(trans('app.import_classes_subject_instructor_error_type',['attributes'=>$attribute]));
                }
            }],
            "instructor_id" => "required|exists:users,id",
            "thu" => 'required|boolean',
            "fri" => 'required|boolean',
            "sat" => 'required|boolean',
            "start_date_time" => 'after:now',
            "repeat" => [
                'required',
                Rule::in([0, 3])
            ],
        ];

        if ($data and isset($data["from"])) {
            $rules['until_date'] = 'nullable|date|after:now|required_if:repeat,==,3|before:'.Carbon::parse($data["from"])->addMonths(6)->format('Y-m-d');
        }

        return $rules;
    }

    private function prepareData(array $row): array
    {
        $data = [
            "subject_id" => $row["subject_id"] ?? null,
            "instructor_id" => $row["instructor_id"] ?? null,
            "sun" => $row["sunday"] ? 1 : 0,
            "sun_from" => null,
            "sun_to" => null,
            "mon" => $row["monday"] ? 1 : 0,
            "mon_from" => null,
            "mon_to" => null,
            "tue" => $row["tuesday"] ? 1 : 0,
            "tue_from" => null,
            "tue_to" => null,
            "wed" => $row["wednesday"] ? 1 : 0,
            "wed_from" => null,
            "wed_to" => null,
            "thu" => $row["thursday"] ? 1 : 0,
            "thu_from" => null,
            "thu_to" => null,
            "fri" => $row["friday"] ? 1 : 0,
            "fri_from" => null,
            "fri_to" => null,
            "sat" => $row["saturday"] ? 1 : 0,
            "sat_from" => null,
            "sat_to" => null,
        ];
            try {
                $data['until_date'] = (new Carbon($row['repeat_until']))->format('Y-m-d');
            } catch (Exception $exception) {
                $data['until_date'] = null;
            }
            try {
                $data['from'] = (new Carbon($row['start_date']))->format('Y-m-d');
            } catch (Exception $exception) {
                $data['from'] = null;
            }

            try {
                $data['to_time'] = (new Carbon($row['time_to']))->format('H:i');
            } catch (Exception $exception) {
                $data['to_time'] = null;
            }

            try {
                $data['from_time'] = (new Carbon($row['time_from']))->format('H:i');
            } catch (Exception $exception) {
                $data['from_time'] = null;
            }

        $data['subject_instructor_branch'] = User::where('type', UserEnums::SCHOOL_INSTRUCTOR)
            ->where('branch_id', $this->importJob->classroom->branch->id)
            ->where('id', $data['instructor_id'] ?? null)
            ->exists();
        $data['subject_instructor_id'] = DB::table('subject_school_instructor')
                ->where('subject_id', $row["subject_id"])
                ->where('instructor_id', $row["instructor_id"])
                ->exists();
        $data["repeat"] = $row["repeat_type"];
        $data["subject_id"] = $row["subject_id"] ?? null;
        $data["start_date_time"] = $row["start_date"] . " " . $row["time_from"];
        return $data;
    }

    private function addError($errors, int $row)
    {
        $this->importJob->increment("has_errors");
        if (is_array($errors)) {
            foreach ($errors as $error) {
                if (is_array($error)) {
                    foreach ($error as $message) {
                        $this->importJob->errors()->create(
                            [
                                'error' => $message,
                                'row' => $row
                            ]
                        );
                    }
                } else {
                    $this->importJob->errors()->create(
                        [
                            'error' => $error,
                            'row' => $row
                        ]
                    );
                }
            }
        } else {
            $this->importJob->errors()->create(
                [
                    'error' => $errors,
                    'row' => $row
                ]
            );
        }

        if ($row-1 == $this->numberOfRows) {
            $this->importJob->update(['status' => ImportJobsStatusEnums::COMPLETED]);
        }
    }

    private function addData(array $data, $rowNo)
    {
        CreateClassroomClassSession::dispatch($this->importJob->classroom, $data, $this->importJob, $rowNo, $rowNo-1==$this->numberOfRows)->onQueue("low")->onConnection('redisOneByOne');
    }
}
