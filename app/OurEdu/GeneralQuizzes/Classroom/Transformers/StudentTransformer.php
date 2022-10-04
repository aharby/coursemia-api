<?php


namespace App\OurEdu\GeneralQuizzes\Classroom\Transformers;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\Users\Models\Student;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class StudentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];
    protected array $availableIncludes = [
    ];
    /**
     * @var GeneralQuiz|null
     */
    private $generalQuiz;

    /**
     * StudentTransformer constructor.
     * @param GeneralQuiz|null $generalQuiz
     */
    public function __construct(GeneralQuiz $generalQuiz = null )
    {

        $this->generalQuiz = $generalQuiz;
    }

    public function transform(Student $student)
    {
        $data =  [
            'id' => (int)$student->user_id,
        ];
        if ($student->user) {
            $data['first_name'] = (string)$student->user->first_name;
            $data['last_name'] = (string)$student->user->last_name;
        }
        if (isset($this->generalQuiz)) {
            $isSelectedStudent = $this->generalQuiz->students()->where("id", "=", $student->user_id)->first();

            $data["is_selected"] = (boolean)isset($isSelectedStudent);
        }

        return $data;
    }
}
