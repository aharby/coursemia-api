<?php


namespace App\OurEdu\Assessments\Lookup\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Lookup\Transformers\GradeClassLookUpTransformer;
use App\OurEdu\GradeClasses\GradeClass;
use \App\OurEdu\GeneralQuizzes\Lookup\Transformers\SubjectLookUpTransformer;
use \App\OurEdu\GeneralQuizzes\Lookup\Transformers\ClassroomLookUptransformer;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Users\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;
use function GuzzleHttp\Psr7\str;

class LookUpTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];

    protected array $availableIncludes = [
        "users"
    ];
    /**
     * @var array
     */
    private $params;
    /**
     * @var Authenticatable|null
     */
    private $user;

    /**
     * LookUpTransformer constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
        $this->user = Auth::guard("api")->user();
    }

    public function transform()
    {
        return [
            'id' => Str::uuid(),
        ];
    }

    public function includeUsers()
    {

        if (isset($this->params['user_type']) && in_array($this->params['user_type'], UserEnums::assessmentUsers())) {
            $users = User::query()->where('type', $this->params['user_type'])
                ->where(function (Builder $query) {
                    if (!is_null($this->user->school_id)) {
                        if ($this->params['user_type'] == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
                            $query->whereHas('schoolAccount',function(Builder $builder){
                                $builder->where('id',$this->user->school_id);
                            });
                        }else{
                            $query->whereHas("branch", function (Builder $builder) {
                                $builder->where("school_account_id", "=", $this->user->school_id);
                            })
                            ->orWhereHas("schoolSupervisor", function (Builder $builder) {
                                $builder->where("school_account_id", "=", $this->user->school_id);
                            })
                            ->orWhereHas("schoolLeader", function (Builder $builder) {
                                $builder->where("school_account_id", "=", $this->user->school_id);
                            })
                            ->orWhereHas('branches', function (Builder $schoolAccountBranch) {
                                $schoolAccountBranch->where("school_account_id", "=", $this->user->school_id);
                            })
                            ->orWhere('school_id', $this->user->school_id);
                        }
                    }
                });
            return $this->collection(
                $users->cursor(),
                new UserTransformer(),
                ResourceTypesEnums::USER
            );
        }
    }
}
