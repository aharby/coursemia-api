<?php

namespace App\OurEdu\Exams\Repository\PrepareExamQuestion;

use App\OurEdu\Exams\Enums\AptitudeEnums;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PrepareExamQuestionRepository implements PrepareExamQuestionRepositoryInterface
{
    private $prepareExamQuestion;

    public function __construct(PrepareExamQuestion $prepareExamQuestion)
    {
        $this->prepareExamQuestion = $prepareExamQuestion;
    }

    /**
     * @param array $data
     * @return PrepareExamQuestion|null
     */
    public function create(array $data): ?PrepareExamQuestion
    {
        return $this->prepareExamQuestion->create($data);
    }

    /**
     * @param int $id
     * @return PrepareExamQuestion|null
     */
    public function findOrFail(int $id): ?PrepareExamQuestion
    {
        return $this->prepareExamQuestion->findOrFail($id);
    }

    /**
     * @param PrepareExamQuestion $prepareExamQuestion
     * @param array $data
     * @return PrepareExamQuestion|null
     */
    public function update(PrepareExamQuestion $prepareExamQuestion, array $data): ?PrepareExamQuestion
    {
        $prepareExamQuestion->update($data);
        return $this->prepareExamQuestion->findOrFail($prepareExamQuestion->id);
    }

    public function getCountBySubjectFormat($sectionIds, $difficultyLevel)
    {
        return $this->prepareExamQuestion
            ->where('subject_format_subject_id', $sectionIds)
            ->where('difficulty_level', $difficultyLevel)->count();
    }

    public function getBySubjectFormatAndDifficultyLevel($subjectFormatSubjectId, $difficultyLevel, $limit)
    {
        return $this->prepareExamQuestion
            ->where('subject_format_subject_id', $subjectFormatSubjectId)
            ->where('difficulty_level', $difficultyLevel)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function getBySubjectFormat($subjectFormatSubjectId)
    {
        return $this->prepareExamQuestion
            ->where('subject_format_subject_id', $subjectFormatSubjectId)
            ->inRandomOrder()
            ->get();
    }

    public function getBySubjectFormatIn(array $subjectFormatSubjectIds)
    {
        return $this->prepareExamQuestion
            ->whereIn('subject_format_subject_id', $subjectFormatSubjectIds)
            ->inRandomOrder()
            ->get();
    }

    /**
     * @param $sectionIds
     * @param $numberOfQuestions
     * @param $levels
     * @param $pivotName
     * @param int $studentId
     * @return Collection
     */
    public function getSubjectFormatQuestionsCount($sectionIds, $numberOfQuestions, $levels, $pivotName, $studentId = 0)
    {
        $result = DB::select(
            DB::raw(
                "
                    select `subject_format_subject_id`,count(id) as question_count,
                    ceil(count(id) /
                    (Select  sum(count)
                        from (select id, count(id) as Count
                            from prepare_exam_questions
                            where subject_format_subject_id in (" . implode(',', $sectionIds) . ")
                            and difficulty_level in ('" . implode("','", $levels) . "')
                            and is_done
                            and deleted_at IS NULL
                            and not exists
                                    (select * from students inner join {$pivotName}
                                     on `students`.`id` = `{$pivotName}`.`student_id`
                                    where `prepare_exam_questions`.`id` =
                                    `{$pivotName}`.`prepare_exam_question_id` and `id` = {$studentId})
                            group by id) as count) *  {$numberOfQuestions} )as return_question_count
                    from `prepare_exam_questions`
                    where subject_format_subject_id in (" . implode(',', $sectionIds) . ")
                    and is_done
                    and deleted_at IS NULL
                    and difficulty_level in ('" . implode("','", $levels) . "')
                    and not exists
                                    (select * from students inner join {$pivotName}
                                     on `students`.`id` = `{$pivotName}`.`student_id`
                                    where `prepare_exam_questions`.`id` =
                                    `{$pivotName}`.`prepare_exam_question_id` and `id` = {$studentId})
                    group by `subject_format_subject_id`
                "
            )
        );

        return collect($result);
    }

    public function getBySubjectFormats(array $subjectFormatIds)
    {
        return $this->prepareExamQuestion
            ->whereIn('subject_format_subject_id', $subjectFormatIds)
            ->with('question')
            ->inRandomOrder()
            ->get();
    }

    public function getStudentNotTakenQuestions(
        int $studentId,
        int $subjectFormatId,
        int $limit,
        array $levels,
        string $generationType
    ) {
        return $this->prepareExamQuestion
            ->whereDoesntHave("{$generationType}Students", fn($q) => $q->where('id', $studentId))
            ->where('subject_format_subject_id', $subjectFormatId)
            ->whereIn('difficulty_level', $levels)
            ->where('is_done', 1)
            ->with('question')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function detachQuestions(int $studentId, array $subjectFormatIds, array $levels, string $generationType)
    {
        $questionsIds = $this->prepareExamQuestion->newQuery()
            ->whereHas("{$generationType}Students", fn($q) => $q->where('id', $studentId))
            ->whereIn('subject_format_subject_id', $subjectFormatIds)
            ->whereIn('difficulty_level', $levels)
            ->pluck('id')->all();

        //Favor deleting pivot table records over detaching by questions
        \DB::table("prepare_{$generationType}_question_student")
            ->whereIntegerInRaw('prepare_exam_question_id', $questionsIds)
            ->delete();

        return $questionsIds;
    }

    public function getAptitudeTestQuestions(array $subjectFormatIds)
    {
        // if complete aptitude test then load questions based on each section's aptitude_percentage
        $result = collect([]);
        foreach ($subjectFormatIds as $subjectFormatId) {
            // current limit is number of question after applying section percentage
            $slug = SubjectFormatSubject::find($subjectFormatId)->slug;
            $percentage = AptitudeEnums::APTITUDE_PERCENTAGES[$slug] / 100;
            // todo : need to change percentages of every subsection (business)
            $currentLimit = (int)ceil((AptitudeEnums::TOTAL_NUMBER_OF_QUESTIONS / 2) * $percentage);
            $currentResult = $this->prepareExamQuestion
                ->where('subject_format_subject_id', $subjectFormatId)
                ->with('question')
                ->inRandomOrder()
                ->limit($currentLimit)
                ->get();
            $result = $result->merge($currentResult);
        }

        return $result;
    }

    public function getBySubjectSectionsAndDifficultyLevel($sections = [], $difficultyLevel, $limit)
    {
        return $this->prepareExamQuestion
            ->whereIn('subject_format_subject_id', $sections)
            ->where('difficulty_level', $difficultyLevel)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
