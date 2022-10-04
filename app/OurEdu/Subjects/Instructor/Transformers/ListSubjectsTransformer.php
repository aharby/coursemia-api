<?php


namespace App\OurEdu\Subjects\Instructor\Transformers;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Reports\ReportEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;

class ListSubjectsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param Subject $subject
     * @return       array
     */

    public function transform(Subject $subject)
    {


        return [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'section_type' => is_null($subject->section_type) ? 'section' : $subject->section_type,
            'subject_image' => (string) imageProfileApi($subject->image),
            'color' => (string)$subject->color,
        ];
    }
}
