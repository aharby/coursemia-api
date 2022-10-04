<?php

namespace App\OurEdu\Subjects\Instructor\Transformers;

use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class SectionTransformer extends TransformerAbstract
{
    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'sections',
    ];

    /**
     * @param SubjectFormatSubject $subjectFormatSubject
     * @return array
     */
    public function transform(SubjectFormatSubject $subjectFormatSubject)
    {
        return [
            'id' => (int) $subjectFormatSubject->id,
            'title' => (string) $subjectFormatSubject->title,
            'has_sections' => (bool) $subjectFormatSubject->childSubjectFormatSubject()->exists(),
        ];
    }

    /**
     * @param $subject
     * @return \League\Fractal\Resource\Collection
     */

    public function includeSections(SubjectFormatSubject $subjectFormatSubject)
    {
        $subjectFormatSubjects = $subjectFormatSubject->childSubjectFormatSubject()->orderBy('id', 'asc')->get();

        if (count($subjectFormatSubjects) > 0) {
            return $this->collection(
                $subjectFormatSubjects,
                new static($this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }
}
