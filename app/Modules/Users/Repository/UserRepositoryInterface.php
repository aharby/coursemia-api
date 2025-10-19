<?php
declare(strict_types=1);

namespace App\Modules\Users\Repository;

use App\Modules\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\Modules\Users\Models\PasswordReset;
use App\Modules\Users\Models\Student;
use App\Modules\Users\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator;
    /**
     * @param  int  $user_id
     * @return User|null
     */
    public function find(int $user_id): ?User;


    /**
     * @param  string  $email
     * @return User|null
     */
    public function findByEmail(string $email, bool $abilitiesUser = false): ?User;

    /**
     * @param  string  $username
     * @return User|null
     */
    public function findByUsername(string $username): ?User;

    /**
     * @param  User  $user
     * @param  array  $data
     * @return bool
     */
    public function update(User $user, array $data): bool;

    /**
     * @param $id
     * @return User|null
     */
    public function findOrFail($id): ?User;

    public function create(array $data): User;


    /**
     * @param  User  $user
     * @return bool
     */
    public function delete(User $user): bool;

    /**
     * @param  string  $type
     * @return array
     */
    public function getPluckUserByType(string $type): array;


    /**
     * @param  array  $data
     * @return PasswordReset
     */
    public function createResetPassword(array $data): PasswordReset;

    /**
     * @param  string  $token
     * @return PasswordReset|null
     */
    public function findResetPasswordToken(string $token): ?PasswordReset;

    /**
     * @param  string  $token
     * @return int
     */
    public function deleteResetPassword(string $token): int;


    /**
     * @param  string  $token
     * @return int
     */
    public function findUserByConfirmToken(string $token): ?User;

    public function findUserByOtpTokenAndConfirmToken(string $otp,string $confirmToken): ?User;

    /**
     * getting the user by his facebook, twitter..etc and type
     * @param  string  $providerId
     * @return User|null
     */
    public function findByProviderAndType(string $provider, string $providerId, $type): ?User;

    /**
     * getting the user by his facebook, twitter..etc
     * @param  string  $providerId
     * @return User|null
     */
    public function findByProvider(string $provider, string $providerId): ?User;

    /**
     * search users using a string
     * @param  string  $q
     * @return User|null
     */
    public function searchUsersByEmailAndType($email, $type): ?Collection;

    /**
     * @return array
     */
    public function getPluckInstructors(): array;

    /**
     * pluck instructors by subjectId
     * @param  int  $subjectId
     * @return Collection
     */
    public function getPluckInstructorsBySubjectId(int $subjectId): Collection;


    /**
     * Increment user wallet balance
     * @param  Student  $user
     * @param  float  $amount
     * @return void
     */
    public function incrementStudentBalance($student, $amount);

    /**
     * @param  User  $user
     * @param  array  $data
     * @return bool
     */
    public function updateStudent(User $user, array $data);

    public function findStudentOrFail($id);

    /**
     * @param $userType
     * @return LengthAwarePaginator
     */
    public function listUsersByType($userType);

    /**
     * @param $id
     * @param $withCountry
     */
    public function addIpAndUserAgentToLogs($id,$withCountry);

    public function getUsersByEmail(array $emails);


    /**
     * @param int $token
     * @return bool
     */
    public function checkConfirmToken(int $token): bool;

    public function getUser();

    public function pluckSchoolUserIdByUserType(SchoolAccount $schoolAccount,string $userType): array;

    public function findUserByOtp(string $code): ?User;

    public function findByPhone(string $phone, string $country_code): ?User;

    public function findResetPasswordTokenByPhoneOrMail(User $user);

}
