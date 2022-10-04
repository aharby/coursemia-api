<?php

namespace App\OurEdu\GeneralQuizzes\Lookup\Transformers;

use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use League\Fractal\TransformerAbstract;

class BranchLookupTransformer extends TransformerAbstract
{
    public function transform(SchoolAccountBranch $branch)
    {
        return [
            'id' => $branch->id,
            'name' => $branch->name,
        ];
    }
}
