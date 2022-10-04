<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\Transformers;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAccounts\SchoolAccounts\Transformers\SchoolAccountTransformer;
use League\Fractal\TransformerAbstract;

class SchoolAccountBranchTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'schoolAccount'
    ];
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }
    public function transform(SchoolAccountBranch $schoolAccountBranch)
    {
        $transformedData = [
            'id' => $schoolAccountBranch->id,
            'school_account_branch_name' => $schoolAccountBranch->name,
        ];
        return $transformedData;
    }

    public function includeSchoolAccount()
    {
        $studentSchoolId = auth()->user()->school_id;
        $schoolAccountStudent = SchoolAccount::whereId($studentSchoolId)->get();
        if ($schoolAccountStudent) {
            return $this->collection($schoolAccountStudent, new SchoolAccountTransformer(), ResourceTypesEnums::SCHOOL_ACCOUNT);
        }
    }

}
