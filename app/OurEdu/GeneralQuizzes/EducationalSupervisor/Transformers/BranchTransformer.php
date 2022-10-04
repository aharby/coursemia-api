<?php

namespace App\OurEdu\GeneralQuizzes\EducationalSupervisor\Transformers;

use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use League\Fractal\TransformerAbstract;

class BranchTransformer extends TransformerAbstract
{

    public function transform(SchoolAccountBranch $branch)
    {
        return [
            'id' => $branch->id,
            'name' => $branch->name,
        ];
    }
}
