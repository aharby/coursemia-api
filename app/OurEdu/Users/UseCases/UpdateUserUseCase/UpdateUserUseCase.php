<?php


namespace App\OurEdu\Users\UseCases\UpdateUserUseCase;


use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\UseCases\UpdateContentAuthorUserCase\UpdateContentAuthorUseCaseInterface;
use App\OurEdu\Users\UseCases\UpdateInstructorUseCase\UpdateInstructorUseCaseInterface;
use App\OurEdu\Users\UseCases\UpdatedminUseCase\UpdateAdminUseCaseInterface;

use App\OurEdu\Users\UseCases\UpdateSchoolAdminUseCase\UpdateSchoolAdminUseCaseInterface;
use App\OurEdu\Users\UseCases\UpdateStudentUseCase\UpdateStudentUseCaseInterface;
use App\OurEdu\Users\UserEnums;


class UpdateUserUseCase implements UpdateUserUseCaseInterface
{
    private $contentAuthorUseCase;
    private $instructorUseCase;
    private $adminUseCase;
    private $updateStudentUseCase;
    private $schoolAdminUseCase;

    /**
     * UpdateUserUseCase constructor.
     * @param UpdateContentAuthorUseCaseInterface $updateContentAuthorUseCase
     * @param UpdateInstructorUseCaseInterface $updateInstructorUseCase
     * @param UpdateSchoolAdminUseCaseInterface $schoolAdminUseCase

     */
    public function __construct(
        UpdateContentAuthorUseCaseInterface $updateContentAuthorUseCase,
        UpdateInstructorUseCaseInterface $updateInstructorUseCase,
        UpdateStudentUseCaseInterface $updateStudentUseCase,
        UpdateSchoolAdminUseCaseInterface $schoolAdminUseCase

    ) {
        $this->contentAuthorUseCase = $updateContentAuthorUseCase;
        $this->instructorUseCase = $updateInstructorUseCase;
        $this->updateStudentUseCase = $updateStudentUseCase;
        $this->schoolAdminUseCase = $schoolAdminUseCase;
    }


    /**
     * @param UserRepositoryInterface $userRepository
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateUser(UserRepositoryInterface $userRepository, array $data, int $id): bool
    {
        $data['language']=Config('app.locale');
        $row = $userRepository->findOrFail($id);
        $user = $userRepository->update($row, $data);
        $userRepository->update( $row->fresh() ,['profile_picture' => 'users/profiles/'.$row->fresh()->profile_picture]);

        if ($row->type == UserEnums::CONTENT_AUTHOR_TYPE) {
            $this->contentAuthorUseCase->UpdateContentUseCase($row->id, $data);
        }
        if ($row->type == UserEnums::INSTRUCTOR_TYPE) {
            $this->instructorUseCase->UpdateInstructorCase($row->id, $data);
        }
        if ($row->type == UserEnums::STUDENT_TYPE) {
            $this->updateStudentUseCase->updateStudentCase($row->id, $data);
        }
        if ($row->type == UserEnums::SCHOOL_ADMIN) {
            $adminData['user_id'] = $row->id;
            $adminData['schools'] = $data['schools'];
            $this->schoolAdminUseCase->updateSchoolAdmin($row->id , $adminData);
        }
        return $user;
    }
}
