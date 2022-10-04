<?php

namespace App\OurEdu\VCRSchedules\Instructor\Transformers;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Transformers\CourseListTransformer;
use App\OurEdu\SubjectPackages\Student\Transformers\ListPackagesTransformer;
use App\OurEdu\Users\Enums\AvailableEnum;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Users\Models\Student;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Invitations\Enums\InvitationEnums;
use App\OurEdu\Courses\Models\SubModels\LiveSession;
use App\OurEdu\Users\Transformers\ListTeacherTransformer;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Profile\Transformers\ActivitiesTransformer;
use App\OurEdu\Profile\Transformers\SentInvitationTransformer;
use App\OurEdu\Courses\Transformers\LiveSessionListTransformer;
use App\OurEdu\Profile\Transformers\ReceivedInvitationTransformer;
use App\OurEdu\Subjects\Student\Transformers\ListSubjectsTransformer;

class StudentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [

    ];
    private $params;

    public function __construct($params)
    {
        $this->params = $params;
        if (isset($this->params['no_action'])) {
            $this->defaultIncludes = [];
        }
    }

    public function transform(Student $student)
    {
        $transformedData = [
            'id' => $student->id,
            'birth_date' => $student->birth_date,
            'educational_system_id' => $student->educational_system_id,
            'school_id' => $student->school_id,
            'class_id' => $student->class_id,
            'academical_year_id' => $student->academical_year_id,
            'wallet_amount' => $student->wallet_amount,
        ];
        return $transformedData;
    }

}
