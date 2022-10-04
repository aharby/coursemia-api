<?php

namespace App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase\V2;

use App\Exceptions\ErrorResponseException;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase\GenerateExamUseCase as ParentGenerateExamUseCase;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase\GenerateExamUseCaseInterface;

class GenerateExamUseCase extends ParentGenerateExamUseCase implements GenerateExamUseCaseInterface
{
    private $sections;

    /**
     * @param $student
     * @param $subjectId
     * @param $sectionIds
     * @param $numberOfQuestions
     * @param $difficultyLevelId
     * @return Exam|array|null
     * @throws ErrorResponseException
     */
    public function generateExam(
        $student,
        $subjectId,
        $sectionIds,
        $numberOfQuestions,
        $difficultyLevelId,
    ) {
        $this->generationType = ExamTypes::EXAM;
        $this->pivotName = 'prepare_exam_question_student';
        $this->sections = getSectionsOfSections($sectionIds);

        $exam = $this->generate($sectionIds, $difficultyLevelId, $student, $numberOfQuestions, $subjectId);

        if ($exam && !$exam['error']) {
            $student->takenExamQuestions()
                ->attach($this->acceptedQuestions->pluck('id')->all());
            //mark sections progress
            markSectionProgress($this->sections, $subjectId, $student);
        }

        return $exam;
    }

    /**
     * @param $student
     * @param $subjectId
     * @param $sectionIds
     * @param $numberOfQuestions
     * @param $difficultyLevelId
     * @return Exam|array
     * @throws ErrorResponseException
     */
    public function generateCompetition(
        $student,
        $subjectId,
        $sectionIds,
        $numberOfQuestions,
        $difficultyLevelId
    ) {
        $this->generationType = ExamTypes::COMPETITION;
        $this->pivotName = 'prepare_competition_question_student';
        $this->sections = getSectionsOfSections($sectionIds);

        $exam = $this->generate($sectionIds, $difficultyLevelId, $student, $numberOfQuestions, $subjectId);

        if ($exam && !$exam['error']) {
            (new ExamRepository($exam))->joinCompetition($student);
            $student->takenCompetitionQuestions()
                ->attach($this->acceptedQuestions->pluck('id')->all());
        }

        return $exam;
    }

    /**
     * @param $sectionIds
     * @param $difficultyLevelId
     * @param $student
     * @param $numberOfQuestions
     * @param $subjectId
     * @return Exam|array|null
     * @throws ErrorResponseException
     */
    private function generate(
        $sectionIds,
        $difficultyLevelId,
        $student,
        $numberOfQuestions,
        $subjectId
    ) {
        $difficultyLevel = $this->optionRepository->find($difficultyLevelId)->slug ?? '';

        // get allowed difficulty levels
        $levels = $this->difficultyFilter($difficultyLevel);

        $requirements = $this->prepareRequiredQuestionsAmount(
            $student->id,
            $this->sections,
            $numberOfQuestions,
            $levels
        );
        if (isset($requirements['error'])) {
            return $requirements;
        }

        foreach ($requirements as $requirement) {
            // get not generated questions for student
            $this->acceptedQuestions = $this->acceptedQuestions->merge(
                $this->prepareExamQuestionRepository->getStudentNotTakenQuestions(
                    $student->id,
                    $requirement->subject_format_subject_id,
                    $requirement->return_question_count,
                    $levels,
                    $this->generationType
                )
            );
        }

        // after all: if the required $numberOfQuestions is bigger than the acceptedQuestions
        if ($numberOfQuestions > $this->acceptedQuestions->count()) {
            // throw exception
            throw new ErrorResponseException(
                trans(
                    'api.not_enough_questions_change_difficulty_level_or_number_of_questions'
                )
            );
        }

        if ($this->acceptedQuestions->count() > $numberOfQuestions) {
            $this->acceptedQuestions = $this->acceptedQuestions->random($numberOfQuestions);
        }

        return $this->createExamAndAssignQuestions(
            $student,
            $subjectId,
            $sectionIds,
            $numberOfQuestions,
            $difficultyLevel
        );
    }
}
