<?php


namespace App\OurEdu\Feedbacks\Repository;


use App\OurEdu\Feedbacks\Feedback;
use Illuminate\Pagination\LengthAwarePaginator;

class FeedbackRepository implements FeedbackRepositoryInterface
{
    private $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function all(): LengthAwarePaginator
    {
        return $this->feedback->orderBy('id','DESC')->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    public function create(array $data): ?Feedback
    {
        return $this->feedback->create($data);
    }

    public function findOrFail(int $id): ?Feedback
    {
        return $this->feedback->findOrFail($id);
    }

    public function update(Feedback $feedback, array $data): bool
    {
        return $feedback->update($data);
    }

    public function delete(Feedback $feedback): bool
    {
        return $feedback->delete();
    }

}
