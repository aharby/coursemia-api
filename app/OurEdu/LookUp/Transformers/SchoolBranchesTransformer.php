<?php


namespace App\OurEdu\LookUp\Transformers;


use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use League\Fractal\TransformerAbstract;

class SchoolBranchesTransformer extends TransformerAbstract
{
    public function transform(SchoolAccountBranch $branch)
    {
        return [
            "id" => (int)$branch->id,
            "name" => (string)$branch->name
        ];
    }
}
