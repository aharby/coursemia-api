<?php

namespace App\OurEdu\Subjects\Admin\Imports;

use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\ParentData;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToCollection, WithValidation, WithHeadingRow
{

    private $data;
    /**
     * @var CreateZoomUserUseCaseInterface
     */
    private $createZoomUser;

    public function __construct(array $data , CreateZoomUserUseCaseInterface  $createZoomUser)
    {
        $this->data = $data;
        $this->createZoomUser = $createZoomUser;
    }

    public function rules(): array
    {
        return [
            'student_first_name' => 'required|max:80',
            'student_last_name' => 'required|max:80',
            'student_id' => ['required',
                function ($attribute, $value, $fail) {
                    $user = User::query()
                        ->where('username', "=", $value)
                        ->where("type", "!=", UserEnums::STUDENT_TYPE)
                        ->first();


                    if ($user) {
                        $message = trans("app.import_parents_students_message_for_wrong_user_types", ['attribute' => $attribute, "user_type" => UserEnums::getLabel($user->type)]);
                        if ($user->type == UserEnums::PARENT_TYPE) {
                            $students = $user->students;

                            $list = "<br> <ul>";

                            foreach ($students as $student) {
                                $list .= "<li>" . trans("app.parent_student", [
                                        "name"      => $student->name,
                                        'school'    => ("<spam style='color: green'>" .  ($student->school->name ?? "") . "</spam>"),
                                        'classroom' => ("<spam style='color: green'>" . (  $student->student->classroom->name ?? '') ."</spam>"),
                                    ]) . "</li>";
                            }

                            $list .= "<li>" . trans("app.please use another Id") . "</li>";
                            $list .= "</ul>";

                            $message .= " " . trans("app.and have students in") . " " . $list;

                            return $fail($message);
                        }
                        $message .= " " .trans("app.please use another Id");

                        $fail($message);
                    }
                }
            ],
            'parent_1_first_name ' => 'max:80',
            'parent_1_last_name ' => 'max:80',
            'parent_1_mobile' => 'nullable|integer',
            'parent_1_id' => ['nullable',
                function ($attribute, $value, $fail) {
                    $user = User::query()
                        ->where('username', "=", $value)
                        ->where("type", "!=", UserEnums::PARENT_TYPE)
                        ->first();

                    if ($user) {
                        if ($user->type == UserEnums::STUDENT_TYPE) {
                            $student = $user->student;
                            $fail(trans("app.import_parents_message_for_wrong_user_types", [
                                'attribute' => $attribute,
                                "user_type" => UserEnums::getLabel($user->type),
                                'school'    => ("<spam style='color: green'>" .  ($user->school->name ?? "") . "</spam>"),
                                'classroom' => ("<spam style='color: green'>" . (  $student->classroom->name ?? '') ."</spam>"),
                            ]));
                        } else {
                            $fail(trans("app.import_parents_students_message_for_wrong_user_types", ['attribute' => $attribute, "user_type" => UserEnums::getLabel($user->type)]));
                        }
                    }
                }],
            'parent_2_mobile' => 'nullable|integer',
            'parent_2_first_name ' => 'nullable|max:80',
            'parent_2_last_name ' => 'nullable|max:80',
            'parent_2_id' => ['nullable',
                function ($attribute, $value, $fail) {
                    $user = User::query()
                        ->where('username', "=", $value)
                        ->where("type", "!=", UserEnums::PARENT_TYPE)
                        ->first();
                    if ($user) {

                        if ($user->type == UserEnums::STUDENT_TYPE) {
                            $student = $user->student;
                            $fail(trans("app.import_parents_message_for_wrong_user_types", [
                                'attribute' => $attribute,
                                "user_type" => UserEnums::getLabel($user->type),
                                'school'    => ("<spam style='color: green'>" .  ($user->school->name ?? "") . "</spam>"),
                                'classroom' => ("<spam style='color: green'>" . (  $student->classroom->name ?? '') ."</spam>"),
                            ]));
                        } else {
                            $fail(trans("app.import_parents_students_message_for_wrong_user_types", ['attribute' => $attribute, "user_type" => UserEnums::getLabel($user->type)]));
                        }


                    }
                }],
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
//            DB::beginTransaction();

            $classRoom = Classroom::find($this->data['classroom_id']);
            $branchEducationalSystemGradeClass = $classRoom->branchEducationalSystemGradeClass;
            $branchEducationalSystem = $branchEducationalSystemGradeClass->branchEducationalSystem;
            $gradeClassId = $branchEducationalSystemGradeClass->gradeClass;
            $countryId = $branchEducationalSystem->branch->schoolAccount->country_id;

            $password = $row['student_id'];
            $user = User::where('username', $row['student_id'])->first();

            // subscribe student to all classroom subjects
            $subjects = Subject::where('educational_system_id', $branchEducationalSystem->educational_system_id)
                ->where('country_id', $countryId)
                ->where('grade_class_id', $gradeClassId->id)
                ->where('educational_term_id', $branchEducationalSystem->educational_term_id)
                ->where('academical_years_id', $branchEducationalSystem->academic_year_id)
                ->get();
            if (!$user) {
                $user = User::create(
                    [
                        'first_name' => $row['student_first_name'],
                        'last_name' => $row['student_last_name'],
                        'username' => $row['student_id'],
                        'language' => 'ar',
                        'password' => $password,
                        'type' => UserEnums::STUDENT_TYPE,
                        'is_active' => 1,
                        'confirmed' => 1,
                        'country_id' => $countryId,
                        'branch_id' => $branchEducationalSystem->branch_id,
                        'school_id' => $branchEducationalSystem->branch->schoolAccount->id
                    ]
                );
                $student = Student::create(
                    [
                        'user_id' => $user->id,
                        'classroom_id' => $this->data['classroom_id'],
                        'password' => $password,
                        'educational_system_id' => $branchEducationalSystem->educational_system_id,
                        'academical_year_id' => $branchEducationalSystem->academic_year_id,
                        'class_id' => $gradeClassId->id,
                    ]
                );
               $student->subjects()->sync($subjects->pluck('id')->toArray());


            } else {
                $student = $user->student;
                if ($user->type == UserEnums::STUDENT_TYPE) {
                    $user->update(
                        [
                            'school_id' => $branchEducationalSystem->branch->schoolAccount->id,
                            'branch_id' => $branchEducationalSystem->branch_id,
                        ]
                    );
                    $student->update(
                        [
                            'classroom_id' => $this->data['classroom_id'],
                            'password' => $password,
                            'educational_system_id' => $branchEducationalSystem->educational_system_id,
                            'academical_year_id' => $branchEducationalSystem->academic_year_id,
                            'class_id' => $gradeClassId->id,
                        ]
                    );
                    $student->subjects()->sync($subjects->pluck('id')->toArray());
                }
            }
            // add parents
            if (!empty($row['parent_1_first_name']) && !empty($row['parent_1_last_name']) && !empty($row['parent_1_mobile']) && !empty($row['parent_1_id'])) {
                $this->createParent($row['parent_1_first_name'], $row['parent_1_last_name'], $row['parent_1_mobile'], $row['parent_1_id'], $user->id, $countryId);
            }
            if (!empty($row['parent_2_first_name']) && !empty($row['parent_2_last_name']) && !empty($row['parent_2_mobile']) && !empty($row['parent_2_id'])) {
                $this->createParent($row['parent_2_first_name'], $row['parent_2_last_name'], $row['parent_2_mobile'], $row['parent_2_id'], $user->id, $countryId);
            }

//            $createZoomUser = $this->createZoomUser->createUser($user);
//            if ($createZoomUser['error']) {
//                DB::rollBack();
//                Log::channel('slack')->error($createZoomUser['detail']);
//                throw ValidationException::withMessages( ['zoom' => $createZoomUser['detail']]);
//
//            }
//
//
//            DB::commit();

        }
    }

    public function createParent($firstName, $lastName, $mobile, $id, $studentId, $countryId)
    {
        $parent = User::where('username', $id)->first();
        if (!$parent) {
            $password = $id;
            $parent = User::create(
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $id,
                    'language' => lang(),
                    'mobile' => $mobile,
                    'type' => UserEnums::PARENT_TYPE,
                    'is_active' => 1,
                    'confirmed' => 1,
                    'country_id' => $countryId,
                    'password' => $password
                ]
            );

            ParentData::create(
                [
                    'user_id' => $parent->id,
                    'password' => $password
                ]
            );
        }
        if ($parent->type == UserEnums::PARENT_TYPE) {
            $parent->students()->syncWithoutDetaching($studentId);
        }
    }

    function userUserNumber($number)
    {
        // query the database and return a boolean
        // for instance, it might look like this in Laravel
        return User::where('username', $number)->exists();
    }
}
