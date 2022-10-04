<?php


namespace App\OurEdu\Feedbacks\Repository;


use App\OurEdu\Feedbacks\Feedback;
use Illuminate\Pagination\LengthAwarePaginator;

interface FeedbackRepositoryInterface
{
    public function all(): LengthAwarePaginator;

    public function create(array $data): ?Feedback;

    public function findOrFail(int $id): ?Feedback;

    public function update(Feedback $feedback, array $data): bool;

    public function delete(Feedback $feedback) : bool;

}
