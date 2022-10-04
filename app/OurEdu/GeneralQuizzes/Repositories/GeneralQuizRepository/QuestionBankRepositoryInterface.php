<?php


namespace App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz ;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
interface QuestionBankRepositoryInterface
{
    public function create(array $data);
    public function update(int $id, array $data);
    public function findGeneralQuizQuestion(GeneralQuiz $generalQuiz, int $questionId);
    public function getAvailableBankQuestion(GeneralQuiz $quiz, string $publicStatus = "private");
    public function findOrFail($questionBankId): ?GeneralQuizQuestionBank;
    public function findWhere($questionBankId): ?GeneralQuizQuestionBank;
    public function updateOrCreateAnswer($question, $data);

}
