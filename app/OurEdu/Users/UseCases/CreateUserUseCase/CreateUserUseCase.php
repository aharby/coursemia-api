<?php


namespace App\OurEdu\Users\UseCases\CreateUserUseCase;


use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\UseCases\CreateContentAuthorUseCase\CreateContentAuthorUseCaseInterface;
use App\OurEdu\Users\UseCases\CreateInstructorUseCase\CreateInstructorUseCaseInterface;
use App\OurEdu\Users\UseCases\CreateSchoolAdminUseCase\CreateSchoolAdminUseCaseInterface;
use App\OurEdu\Users\UseCases\CreateStudentTeacherUseCase\CreateStudentTeacherUseCaseInterface;
use App\OurEdu\Users\UseCases\CreateStudentUseCase\CreateStudentUseCaseInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Config;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class CreateUserUseCase implements CreateUserUseCaseInterface
{
    private $contentAuthorUseCase;
    private $instructorUseCase;
    private $studentTeacherUseCase;
    private $studentUseCase;
    private $schoolAdminUseCase;

    /**
     * CreateUserUseCase constructor.
     * @param CreateContentAuthorUseCaseInterface $createContentAuthorUseCase
     * @param CreateInstructorUseCaseInterface $createInstructorUseCase
     * @param CreateStudentTeacherUseCaseInterface $createStudentTeacherUseCase
     * @param CreateStudentUseCaseInterface $createStudentUseCase
     * @param CreateSchoolAdminUseCaseInterface $schoolAdminUseCase
     */
    public function __construct(
        CreateContentAuthorUseCaseInterface $createContentAuthorUseCase,
        CreateInstructorUseCaseInterface $createInstructorUseCase,
        CreateStudentTeacherUseCaseInterface $createStudentTeacherUseCase,
        CreateStudentUseCaseInterface $createStudentUseCase,
        CreateSchoolAdminUseCaseInterface $schoolAdminUseCase
    ) {
        $this->contentAuthorUseCase = $createContentAuthorUseCase;
        $this->instructorUseCase = $createInstructorUseCase;
        $this->studentTeacherUseCase = $createStudentTeacherUseCase;
        $this->studentUseCase = $createStudentUseCase;
        $this->schoolAdminUseCase = $schoolAdminUseCase;
    }

    /**
     * @param UserRepositoryInterface $userRepository
     * @param array $data
     * @return User|null
     */
    public function createUser(UserRepositoryInterface $userRepository, array $data): ?User
    {
        $data['language']=Config('app.locale');

        $data['type'] == 'admin' ? $data['is_admin'] = 1 : $data['is_admin'] = 0;
        $user = $userRepository->create($data);
        $userRepository->update( $user ,['profile_picture' => 'users/profiles/'.$user->profile_picture]);

        if ($data['type'] == UserEnums::CONTENT_AUTHOR_TYPE) {
            $contentAuthorData = [];
            $contentAuthorData['user_id'] = $user->id;
            $contentAuthorData['hire_date'] = $data['hire_date'];
            $this->contentAuthorUseCase->CreateContentAuthor($contentAuthorData);
        }
        if ($data['type'] == UserEnums::INSTRUCTOR_TYPE) {
            $instructorData = [];
            $instructorData['user_id'] = $user->id;
            $instructorData['about_instructor'] = $data['about_instructor'];
            $instructorData['hire_date'] = $data['hire_date'];
            $instructorData['school_id'] = $data['school_id'];
            $this->instructorUseCase->CreateInstructor($instructorData);
        }
        if ($data['type'] == UserEnums::STUDENT_TEACHER_TYPE) {
            $studentTeacherData = [];
            $studentTeacherData['user_id'] = $user->id;
            $this->studentTeacherUseCase->CreateStudentTeacher($studentTeacherData);
        }

        if ($data['type'] == UserEnums::STUDENT_TYPE) {
            $studentData['user_id'] = $user->id;
//            $studentData['birth_date'] = $data['birth_date'];
//            $studentData['educational_system_id'] = $data['educational_system_id'];
//            $studentData['school_id'] = $data['school_id'];
//            $studentData['class_id'] = $data['class_id'];
//            $studentData['academical_year_id'] = $data['academical_year_id'];
            $this->studentUseCase->createStudent($studentData);
        }
        if ($data['type'] == UserEnums::SCHOOL_ADMIN) {
            $adminData['user_id'] = $user->id;
            $adminData['schools'] = $data['schools'];
            $this->schoolAdminUseCase->createSchoolAdmin($adminData);
        }
        return $user;
    }


}
