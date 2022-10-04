<?php

namespace App\OurEdu\GeneralExams\Repository\PreparedQuestion;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;


class PreparedGeneralExamQuestionRepository implements PreparedGeneralExamQuestionRepositoryInterface
{
    use Filterable;
    public function create($data)
    {
        if (count($data)) {
            return PreparedGeneralExamQuestion::create($data);
        }

        return;
    }

    public function paginateSectionQuestions($section ,$difficultyLevelId, $filters = [])
    {
        $sections = getSectionChilds($section);
        return $this->applyFilters(new PreparedGeneralExamQuestion() , $filters)
            ->whereIn('subject_format_subject_id', $sections)
            ->where('difficulty_level_id',$difficultyLevelId)
            ->jsonPaginate();
    }

}
