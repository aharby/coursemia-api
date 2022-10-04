<?php

namespace App\OurEdu\SchoolAccounts\SubjectInstructors\SchoolBranchSupervisor\Imports;

use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface;
use App\OurEdu\Assessments\Jobs\AddUserToAssessmentJob;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\UseCases\ResetSchoolInstructorPasswordUseCase\ResetSchoolInstructorPasswordUseCaseInterface;

class InstructorsImport implements ToCollection, WithValidation, WithHeadingRow
{

    private $data;
    private $resetSchoolInstructorPasswordUseCase;
    private $userRepository;
    private $createZoomUser;

    public function __construct(
        array $data,
        ResetSchoolInstructorPasswordUseCaseInterface $resetSchoolInstructorPasswordUseCase,
        UserRepositoryInterface $userRepository,
        CreateZoomUserUseCaseInterface $createZoomUser
    )
    {
        $this->data = $data;
        $this->resetSchoolInstructorPasswordUseCase = $resetSchoolInstructorPasswordUseCase;
        $this->userRepository = $userRepository;
        $this->createZoomUser = $createZoomUser;
    }


    public function rules(): array
    {

        return [
            'first_name' => 'required|max:80',
            'last_name' => 'required|max:80',
            'id' => 'required',
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {


            //validate if instructor assigned to another branch
            $alreadyExistsInstructor = User::where('username', $row['id'])->first();
            if ($alreadyExistsInstructor &&
                $alreadyExistsInstructor->type == UserEnums::SCHOOL_INSTRUCTOR &&
                $alreadyExistsInstructor->branch_id == $this->data['branch_id']) {

                $alreadyExistsInstructor->schoolInstructorSubjects()->syncWithoutDetaching($this->data['subject_id']);


            } elseif (!$alreadyExistsInstructor) {

                $this->createInstructor($row);
            }


        }
    }

    private function createInstructor($row)
    {
//        DB::beginTransaction();
        $schoolInstructorUser = User::create([
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'username' => $row['id'],
            'confirm_token' => $this->generateConfirmToken(),
            'language' => 'ar',
            'confirmed' => 1,
            'type' => UserEnums::SCHOOL_INSTRUCTOR,
            'branch_id' => $this->data['branch_id'],
            'password'=> $row['id']
        ]);

        if ($schoolInstructorUser) {
            $createZoomUser = $this->createZoomUser->createUser($schoolInstructorUser);
            if ($createZoomUser['error']) {
//                DB::rollBack();
                Log::channel('slack')->error($createZoomUser['detail']);
//                throw ValidationException::withMessages( ['zoom' => $createZoomUser['detail'] ]);

            }
            $schoolInstructorUser->schoolInstructorSubjects()->sync($this->data['subject_id']);
//            DB::commit();
            AddUserToAssessmentJob::dispatch($schoolInstructorUser)->afterCommit();
        }
    }

    private function generateConfirmToken()
    {
        $token = rand(000000, 999999);
        if ($this->userRepository->checkConfirmToken($token)) {
            return $this->generateConfirmToken();
        }
        return $token;
    }
}
