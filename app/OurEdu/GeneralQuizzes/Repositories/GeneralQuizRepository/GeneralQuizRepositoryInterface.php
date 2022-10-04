<?php


namespace App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface GeneralQuizRepositoryInterface
{

    /**
     * @param $data
     * @return GeneralQuiz
     */
    public function create($data): GeneralQuiz;


    /**
     * @param $data
     * @return bool
     */
    public function update($data): bool;


    /**
     * @return bool
     */
    public function delete(): bool;


    /**
     * @param $generalQuizId
     * @return GeneralQuiz|null
     */
    public function findOrFail($generalQuizId): ?GeneralQuiz;


    /**
     * @param $generalQuizId
     * @return GeneralQuiz|null
     */
    public function findOrFailByMultiFields($generalQuizId, $filters): ?GeneralQuiz;


    public function setGeneralQuiz(GeneralQuiz $generalQuiz);

    public function getGeneralQuiz();

    public function saveGeneralQuizClassrooms(GeneralQuiz $generalQuiz, $classroomIds);

    public function saveGeneralQuizSections(GeneralQuiz $generalQuiz, $sectionsIds);

    public function saveGeneralQuizStudents(GeneralQuiz $generalQuiz, $studentIds);

    public function getGeneralQuizQuestions(GeneralQuiz $generalQuiz);

    public function getGeneralQuizQuestionsPaginated(GeneralQuiz $quiz);

    public function returnQuestion(int $page,$questionsOrder): ?LengthAwarePaginator;

    public function listStudentAvailableGeneralQuizzes(string|array $quizType, array $filters = []);

    public function students(GeneralQuiz $generalQuiz,$paginate = null);

    public function updateGeneralQuizMark(GeneralQuiz $generalQuiz);

    public function listInstructorGeneralQuizzes(User $instructor,$quizType, $subject_id, $gradeClassId, $date,$report = false , $courseId=null);

    public function listInstructorGeneralQuizzesWithoutPagination(User $instructor,$quizType, $subject_id, $gradeClassId, $date,$report = false, $courseId=null);

    public function listEducationalSupervisorGeneralQuizzes($eduSupervisor, $filters , $classroom = null, $type = null, $instructor=null, $date=null, $paginate = true, array $data = []);

    public function listGeneralQuizzes(array $data, $query = null);

    public function trashedClassroomGeneralQuizzes(int $id, bool $paginated = true);

    public function listGeneralQuizzesWithoutPagination(array $data);

    public function getGeneralQuizStudent(GeneralQuiz $generalQuiz);

    public function getStudentGeneralQuizzesByParent(User $studentUser,array $filters);

    public function getGeneralQuizStudents(GeneralQuiz $generalQuiz, bool $isPaginate = true);

    public function exportGeneralQuizzes(array $data, $query = null);

    public function getGeneralQuizStudentAnswers(GeneralQuiz $generalQuiz);


}
