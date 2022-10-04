<?php


namespace App\OurEdu\Assessments\AssessmentManager;


use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class AssignAssessmentRelationDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * @var Assessment
     */
    private $assessment;
    /**
     * @var string
     */
    private $relation;
    /**
     * @var string
     */
    private $userType;
    /**
     * @var array
     */
    private $data;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * AssignAssessmentRelationDataJob constructor.
     * @param Assessment $assessment
     * @param string $relation
     * @param string $userType
     * @param array $data
     */
    public function __construct(Assessment $assessment ,string $includes, array $data = [])
    {
        $this->assessment = $assessment;
        $this->includes = $includes;
        $this->data = $data;
        $this->userRepository = app(UserRepositoryInterface::class);
    }

    public function handle()
    {
        $schoolAccount = $this->assessment->schoolAccount()->first();
        $data = $this->data;
        foreach(explode(',',$this->includes) as $key => $relation){
            if (method_exists($this->assessment, $relation) && isset($data[$relation])) {

                if (!count($data[$relation]) and $schoolAccount) {
                    switch ($relation) {
                        case 'assessors':
                            $data[$relation] = $this->userRepository
                                ->pluckSchoolUserIdByUserType($schoolAccount, $this->assessment->assessor_type);
                            break;
                        case 'assessees':
                            $data[$relation] = $this->userRepository
                                ->pluckSchoolUserIdByUserType($schoolAccount, $this->assessment->assessee_type);
                            break;
                        default:
                            break;
                    }
                }

                if (count($data[$relation])) {
                    $this->assessment->{$relation}()->sync($data[$relation]);
                }
            }
        }
        $this->assessment->resultViewers()->detach();
        foreach ($this->data['resultViewers'] as $resultViewer) {
            $users = $resultViewer->users->count() > 0 ? $resultViewer->users->pluck('id')->toArray() : $this->userRepository
                ->pluckSchoolUserIdByUserType($schoolAccount, $resultViewer->user_type);
            $this->assessment->resultViewers()->syncWithoutDetaching($users);
        }
    }
}
