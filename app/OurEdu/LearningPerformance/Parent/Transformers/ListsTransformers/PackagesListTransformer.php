<?php


namespace App\OurEdu\LearningPerformance\Parent\Transformers\ListsTransformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SubjectPackages\Package;
use App\OurEdu\Users\Models\Student;
use League\Fractal\TransformerAbstract;

class PackagesListTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
    ];

    private $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    /**
     * @param Package $package
     * @return array
     */
    public function transform(Package $package)
    {
        return [
            'id' => (int)$package->id,
            'name' => (string) $package->name,
            'description' => (string) $package->description,
            'price' =>  (int) $package->price.' '.trans('subject_packages.riyal'),
            'package_image' => (string) imageProfileApi($package->picture, 'small'),
            'is_subscribe' => is_student_subscribed_to_package($package , $this->student->user),
        ];
    }

    /**
     * @param $package
     * @return \League\Fractal\Resource\Collection
     */
    public function includeActions(Package $package)
    {
        $actions = [];

        if (! is_student_subscribed_to_package($package , $this->student->user)) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.parent.subscriptions.post.subjectPackageSubscribe',
                    ['id' => $package->id,'studentId'=> $this->student->id]),
                'label' => trans('app.Subscribe'),
                'method' => 'POST',
                'key' => APIActionsEnums::SUBJECT_PACKAGE_SUBSCRIBE
            ];
        }
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}

