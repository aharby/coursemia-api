<?php
declare(strict_types=1);

namespace App\Modules\Users\Repository;

use App\Modules\BaseApp\Traits\Filterable;
use App\Modules\Ratings\Rating;
use App\Modules\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\Modules\Subjects\Models\Subject;
use App\Modules\Subjects\Models\SubModels\SubjectInstructors;
use App\Modules\Users\Models\PasswordReset;
use App\Modules\Users\Models\Student;
use App\Modules\Users\User;
use App\Modules\Users\UserEnums;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Models\Audit;

class UserRepository implements UserRepositoryInterface
{
    use Filterable;


    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param int $user_id
     * @return User|null
     */
    public function find(int $user_id): ?User
    {
        return User::find($user_id);
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email, bool $abilitiesUser = false): ?User
    {
        return User::query()
            ->where('email', $email)->first();
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function findByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    /**
     * @param User $user
     * @param array $data
     * @return bool
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * @return LengthAwarePaginator
     */
    public function all(array $filters = []): LengthAwarePaginator
    {
        $model = $this->applyFilters(new User(), $filters);
        return $model->orderBy('id', 'DESC')->where('super_admin', 0)->jsonPaginate(env('PAGE_LIMIT', 8));
    }

    /**
     * @param $id
     * @return User|null
     */
    public function findOrFail($id): ?User
    {
        return User::findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }


    /**
     * @param User $user
     * @return bool
     * @throws Exception
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function getPluckUserByType(string $type): array
    {
        return User::where('type', $type)->get()->pluck('name', 'id')->toArray();
    }


    /**
     * @param array $data
     * @return PasswordReset
     */
    public function createResetPassword(array $data): PasswordReset
    {
        return PasswordReset::create($data);
    }

    /**
     * @param string $token
     * @return PasswordReset|null
     */
    public function findResetPasswordToken(string $token): ?PasswordReset
    {
        return PasswordReset::where('token', $token)->first();
    }

    /**
     * @param string $token
     * @return int
     * @throws Exception
     */
    public function deleteResetPassword(string $token): int
    {
        return PasswordReset::where('token', $token)->delete();
    }

    public function findUserByConfirmToken(string $token): ?User
    {
        return User::where('confirm_token', $token)->first();
    }

    /**
     * search parents based on string
     * @param string $q
     * @return mixed
     */
    public function searchUsersByEmailAndType($email, $type): ?Collection
    {
        return User::where('email', 'LIKE', "%{$email}%")
            ->where('type', $type)
            ->get();
    }

    public function getPluckInstructors(): array
    {
        return User::where('type', UserEnums::INSTRUCTOR_TYPE)->get()->pluck('name', 'id')->toArray();
    }

    public function getSchoolBranchInstructors($branch_id, $with = []): LengthAwarePaginator
    {
        return User::where('type', UserEnums::SCHOOL_INSTRUCTOR)
            ->where('branch_id', $branch_id)
            ->with($with)
            ->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    public function getSchoolBranchInstructorRatings($branch_id, $instructor_id): LengthAwarePaginator
    {
        return Rating::query()
            ->where('instructor_id', $instructor_id)
            ->whereHas("user")
            ->with('instructor')
            ->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    public function getPluckInstructorsBySubjectId(int $subjectId): Collection
    {
        return Subject::where('id', $subjectId)->first()
            ->instructors()->get()->pluck('name', 'id');
    }

    public function pluckStudentsMail()
    {
        return User::where('type', UserEnums::STUDENT_TYPE)->pluck('email', 'id');
    }

    /**
     * Increment user wallet balance
     * @param Student $student
     * @param float $amount
     * @return void
     */
    public function incrementStudentBalance($student, $amount)
    {
        return $student->increment('wallet_amount', $amount);
    }


    /**
     * @param User $user
     * @param array $data
     * @return int
     */
    public function updateStudent(User $user, array $data)
    {
        return $user->student()->update($data);
    }


    /**
     * getting the user by his facebook, twitter..etc and type
     * @param string $provider
     * @param string $providerId
     * @param string $type
     * @return User|null
     */
    public function findByProviderAndType(string $provider, string $providerId, $type): ?User
    {
        return User::where("{$provider}_id", $providerId)
            ->whereType($type)
            ->first();
    }

    /**
     * getting the user by his facebook, twitter..etc
     * @param string $provider
     * @param string $providerId
     * @return User|null
     */
    public function findByProvider(string $provider, string $providerId): ?User
    {
        return User::where("{$provider}_id", $providerId)
            ->first();
    }

    public function findStudentOrFail($id)
    {
        return Student::findOrFail($id);
    }

    public function listUsersByType($userType)
    {
        return User::where('type', $userType)
            ->active()
            ->take(20)
            ->get();
    }

    public function addIpAndUserAgentToLogs($id, $withCountry)
    {
        $auditRow = Audit::where('auditable_type', Student::class)
            ->where('auditable_id', User::find($id)->student->id)
            ->where('event', 'updated')
            ->orderBy('created_at', 'desc')
            ->first();
        $newValuesAuditRowData = $auditRow->new_values;
        $oldValuesAuditRowData = $auditRow->old_values;

        if ($withCountry) {
            $auditCountryRow = Audit::where('auditable_type', 'user')
                ->where('auditable_id', $id)
                ->where('event', 'updated')
                ->orderBy('created_at', 'desc')
                ->first();

            $oldValuesAuditRowData['country_id'] = $auditCountryRow->old_values['country_id'] ?? '';
            $oldValuesAuditRowData['country_name'] = $auditCountryRow->old_values['country_name'] ?? '';

            $newValuesAuditRowData['country_id'] = $auditCountryRow->new_values['country_id'] ?? '';
            $newValuesAuditRowData['country_name'] = $auditCountryRow->new_values['country_name'] ?? '';
        }

        $newValuesAuditRowData['ip_address'] = $auditRow->ip_address;
        $newValuesAuditRowData['user_agent'] = $auditRow->user_agent;

        $auditRow->update(['new_values' => $newValuesAuditRowData]);
        $auditRow->update(['old_values' => $oldValuesAuditRowData]);
    }

    public function getUsersByEmail(array $emails)
    {
        return User::whereIn('email', $emails)->get();
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $token
     * @return bool
     */
    public function checkConfirmToken(int $token): bool
    {
        return User::where('confirm_token', $token)->exists();
    }

    public function setUserStatus($status)
    {
        $user = User::find(Auth::id());
        $user->status = $status;
        $user->save();
        return $user;
    }

    public function pluckSchoolUserIdByUserType(SchoolAccount $schoolAccount, string $userType): array
    {
        if ($userType == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
            $users = [$schoolAccount->manager_id];

            return $users;
        }

        $users = User::query()
            ->where(
                function (Builder $query) use ($schoolAccount) {
                    $query->whereHas(
                        "branch",
                        function (Builder $builder) use ($schoolAccount) {
                            $builder->where("school_account_id", "=", $schoolAccount->id);
                        }
                    )
                    ->orWhereHas(
                        "schoolSupervisor",
                        function (Builder $builder) use ($schoolAccount) {
                            $builder->where("school_account_id", "=", $schoolAccount->id);
                        }
                    )
                    ->orWhereHas(
                        "schoolLeader",
                        function (Builder $builder) use ($schoolAccount) {
                            $builder->where("school_account_id", "=", $schoolAccount->id);
                        }
                    )
                    ->orWhereHas(
                        'branches',
                        function (Builder $schoolAccountBranch) use ($schoolAccount) {
                            $schoolAccountBranch->where("school_account_id", "=", $schoolAccount->id);
                        }
                    )
                    ->orWhere('school_id', "=", $schoolAccount->id);
                }
            )
            ->where("type", "=", $userType)
            ->pluck("id")
            ->toArray();

        return $users;
    }

    public function findUserByOtpTokenAndConfirmToken(string $otp, string $confirmToken): ?User
    {
        return User::query()->where('otp', $otp)->where('confirm_token', $confirmToken)->first();
    }

    public function findUserByOtp(string $otp): ?User
    {
        return User::query()->where('otp', $otp)->first();
    }

    public function findByPhone(string $phone, string $country_code): ?User
    {
        return User::query()
            ->where('phone', $phone)
            ->where('country_code', $country_code)
            ->first();
    }

    public function findResetPasswordTokenByPhoneOrMail(User $user)
    {
        if ($user->mobile and $resetToken = PasswordReset::query()->where('mobile', $user->mobile)->first()) {
            return $resetToken;
        }

        if ($user->email and $resetToken = PasswordReset::query()->where('email', $user->email)->first()) {
            return $resetToken;
        }

        return null;
    }
}
