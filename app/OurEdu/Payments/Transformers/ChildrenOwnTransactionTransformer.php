<?php

namespace App\OurEdu\Payments\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\SubjectPackages\Package;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\VCRSchedules\Models\VCRRequest;
use League\Fractal\TransformerAbstract;

class ChildrenOwnTransactionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];

    protected array $availableIncludes = [
        'user'
    ];

    public function transform($transaction)
    {
        $subscribableName = $this->resolveSubscribableName($transaction);
        return [
            'id' => $transaction->id,
            'date_time' => $transaction->created_at->format('Y-m-d H:i:s'),
            'amount' => $transaction->amount . " " . trans("app.SAR"),
            'description' => trans(
                'app.transaction_desc',
                [
                    'name' => $transaction->sender->name,
                    'amount' => $transaction->amount . " " . trans("app.SAR"),
                    'subscribe_name' => $subscribableName
                ]
            )
        ];
    }

    private function resolveSubscribableName($transaction)
    {
        $subscribable = $transaction->detail->subscribable;
        $subscribableType = $transaction->detail->subscribable_type;
        $type = "";
        $name = "";

        switch ($subscribableType) {
            case Subject::class:
                $type = trans('app.Subject');
                $name = $subscribable->name;
                break;
            case Course::class:
                $type = trans('app.' . CourseEnums::getFormattedTypes($subscribable->type));
                $name = $subscribable->name;
                break;
            case Package::class:
                $type = trans('app.SubjectPackage');
                $name = $subscribable->name;
                break;
            case VCRRequest::class:
                $type = trans('app.vcr_request');
                break;
        }

        return $type . " " . $name;
    }

    public function includeUser($transaction)
    {
        return $this->item(
            $transaction->sender,
            new UserTransformer(),
            ResourceTypesEnums::USER
        );
    }
}
