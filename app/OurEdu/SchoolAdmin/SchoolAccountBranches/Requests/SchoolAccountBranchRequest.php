<?php
namespace App\OurEdu\SchoolAdmin\SchoolAccountBranches\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use Illuminate\Validation\Rule;

class SchoolAccountBranchRequest extends BaseAppRequest
{
    public function rules()
    {
        return [
            'name' => 'required|max:190',
            'educational_systems' => 'array',
            'supervisor_id' => [
                'nullable',
                Rule::unique('users','username')->where(function ($query) {
                    return $query->where('deleted_at', null);
                }),
            ],
            'leader_id' => [
                'nullable',
                Rule::unique('users','username')->where(function ($query) {
                    return $query->where('deleted_at', null);
                }),
            ],
        ];
    }
}
