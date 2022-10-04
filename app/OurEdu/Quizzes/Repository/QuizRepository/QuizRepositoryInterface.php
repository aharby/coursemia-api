<?php

declare(strict_types=1);

namespace App\OurEdu\Quizzes\Repository\QuizRepository;

use App\OurEdu\Quizzes\Models\QuizQuestion;
use App\OurEdu\Quizzes\Models\QuizQuestionOption;
use App\OurEdu\Quizzes\Models\StudentQuiz;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\Schools\School;
use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface QuizRepositoryInterface
{
    /**
     * @return LengthAwarePaginator
     */
    public function getAllQuizzes(): LengthAwarePaginator;

    /**
     * @param $user
     * @return LengthAwarePaginator
     */
    public function getAllQuizzesByUser($user , $filters = []): LengthAwarePaginator;

    /**
     * @param $user
     * @return LengthAwarePaginator
     */
    public function getAllQuizzesTypesByUser($user , $filters = []): LengthAwarePaginator;



    /**
     * @param $user
     * @return LengthAwarePaginator
     */
    public function getAllHomeWorksByUser($user , $filters = []): LengthAwarePaginator;


    public function getAllPeriodicTestsByUser($user , $filters = []): LengthAwarePaginator;


    /**
     * @param $classroomSessionId
     * @param bool $isPublished
     * @return LengthAwarePaginator
     */
    public function getSessionQuizzes($classroomSessionId, $isPublished = false): LengthAwarePaginator;

    /**
     * @param $classroomSessionId
     * @param bool $isPublished
     * @return LengthAwarePaginator
     */
    public function getSessionHomework($classroomSessionId, $isPublished = false): LengthAwarePaginator;

    /**
     * @param $classroomSessionId
     * @param bool $isPublished
     * @return LengthAwarePaginator
     */
    public function getSessionPeriodicTest($classroomSessionId, $isPublished = false): LengthAwarePaginator;


    /**
     * @param Quiz $quiz
     * @return QuizRepository
     */
    public function setQuiz(Quiz $quiz): QuizRepository;

    /**
     * @return Quiz
     */
    public function getQuiz(): Quiz;

    /**
     * @param $quizId
     * @return Quiz|null
     */
    public function findOrFail($quizId): ?Quiz;

    /**
     * @return bool
     */
    public function delete(): bool;

    /**
     * @param $data
     * @return bool
     */
    public function update($data): bool;

    /**
     * @param $data
     * @return Quiz
     */
    public function create($data):Quiz;

    /**
     * @param $data
     * @return QuizQuestion
     */
    public function createQuestion($data): QuizQuestion;

    /**
     * @param $questionId
     * @param $data
     * @return QuizQuestion
     */
    public function updateQuestion($questionId, $data): QuizQuestion;

    /**
     * @param $questionId
     * @return QuizQuestion|null
     */
    public function findQuestionOrFail($questionId) :?QuizQuestion;

    public function findOptionOrFail($optionId) :?QuizQuestionOption;

    public function createOption($question, $optionData);

    public function updateOption($optionId , $question, $optionData);

    public function getQuestionsIds();

    public function deleteDeletedQuestionsOptions(array $questionsIds);

    public function deleteQuestionsIds(array $questionsIds);

    public function getQuestionOptionsIds($questionId);

    public function deleteOptions($questionId, $optionsIds);

    public function returnQuestion($page, $questionsOrder): ?LengthAwarePaginator;

    public function deleteQuestionAnswers(QuizQuestion $quizQuestion, $studentId);

    public function insertAnswer(QuizQuestion $quizQuestion, $answerData);

    public function insertManyAnswers(QuizQuestion $quizQuestion, $answers);

    public function listAllQuizStudents($quizId): ?LengthAwarePaginator;

    public function listQuizStudents($quizId):? LengthAwarePaginator;

    public function getStudentQuiz($quizId, $studentId):?StudentQuiz;

    public function listBranchQuizzes(SchoolAccountBranch $branch, Request $request);

    public function getClassroomHomework(Student $student);

    public function getStudentQuizzesByParent($studentId , $filters = []);

    public function getStudentTakenQuizzesByParent($studentId);

    public function getStudentPeriodicTest(Student $student);

    public function getReadyNotifyHomeWorkAndPeriodicTest($interval);

    public function getRunningQuizDetails($homeWorkID, $type = null);

    public function schoolQuizzes(SchoolAccount $schoolAccount, array $data = []);
}
