<?php


namespace App\OurEdu\SubjectPackages\Student\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SubjectPackages\Package;
use App\OurEdu\Subjects\Student\Transformers\ListSubjectsTransformer;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\DB;
use League\Fractal\TransformerAbstract;

class PackageTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
        'subjects'
    ];
    protected array $availableIncludes = [];
    private $params;

    public function __construct($params = [], $user = null)
    {
        $this->params = $params;
        $this->user = $user ?? new User;
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
            'price' =>  (int) $package->price . ' ' . trans('subject_packages.riyal'),
            'package_image' => (string) imageProfileApi($package->picture, 'large'),
            'is_subscribe' => is_student_subscribed_to_package($package, $this->user),

        ];
    }

    public function includeSubjects($package)
    {
        $params['view_inside_package'] = true;
        return $this->collection($package->subjects, new ListSubjectsTransformer($params, $this->user), ResourceTypesEnums::SUBJECT);
    }

    public function includeActions(Package $package)
    {
        $actions = [];
        if (auth()->user()->type == UserEnums::PARENT_TYPE) {

            if (isset($this->user->student)) {
                $userIsSubscriped = DB::table('packages_subscribed_students')
                    ->where('package_id', $package->id)
                    ->where('student_id', $this->user->student->id)
                    ->exists();
                if (!$userIsSubscriped) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.parent.subscriptions.post.subjectPackageSubscribe', ['id' => $package->id, 'studentId' => $this->user->student->id]),
                        'label' => trans('app.Subscribe'),
                        'method' => 'POST',
                        'key' => APIActionsEnums::SUBJECT_PACKAGE_SUBSCRIBE
                    ];
                }
            }
            if (count($actions) > 0) {
                return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
            }
        }

        if (auth()->user()->type == UserEnums::STUDENT_TYPE) {
            $userIsSubscribed = DB::table('packages_subscribed_students')
                ->where('package_id', $package->id)
                ->where('student_id', auth()->user()->student->id)
                ->exists();

            if (!$userIsSubscribed) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.student.subjectPackages.post.subscribe.package', ['packageId' => $package->id]),
                    'label' => trans('subject_package.Subscribe'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::SUBSCRIBE
                ];
            }
            if (count($actions) > 0) {
                return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
            }
        }
    }
}
