<?php

namespace App\OurEdu\GeneralQuizzes\Homework\Student\Middleware;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\Users\Models\Student;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentHasHomework
{
    private GeneralQuizRepositoryInterface $quizRepository;
    /**
     * @var Builder|Builder[]|Collection|Model|null
     */
    private $homework;

    public function __construct(GeneralQuizRepositoryInterface $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->homework = GeneralQuiz::with('classrooms', 'students')->findOrFail($request->homeworkId);
        $classroomsCount = $this->homework->classrooms->count();

        $generalQuizStudent = $this->homework->students()->pluck("id")->toArray();
        if (!$generalQuizStudent) {
            $quizClassrooms = $this->homework->classrooms()->pluck("id")->toArray();

            $generalQuizStudent  = Student::query()
                ->whereIn("classroom_id", $quizClassrooms)
                ->pluck("user_id")
                ->toArray();
        }


        if (count($generalQuizStudent) and in_array(Auth::id(), $generalQuizStudent)) {
            return $next($request);
        } else {
            return unauthorize();
        }
    }

    private function studentSpecificHomework(): bool
    {
        if ($this->homework->students()->exists()) {
            return $this->homework->students()->where('users.id', auth()->id())->exists();
        }
        return true;
    }
}
