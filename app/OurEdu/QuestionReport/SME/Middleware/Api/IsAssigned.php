<?php


namespace App\OurEdu\QuestionReport\SME\Middleware\Api;

use App\OurEdu\Subjects\Models\Subject;

class IsAssigned
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $sme = auth()->user();
        $subject = Subject::where('id', $request->route('subject'))->first();

        if ($subject) {
            if ($subject->sme_id == $sme->id) {
                return $next($request);
            } else {
                return formatErrorValidation([
                    'status' => 403,
                    'title' => 'You have to be assigned on this subject first',
                    'detail' => trans('subject.You have to be assigned on this subject first')
                ]);
            }
        } else {
            return formatErrorValidation([
                'status' => 404,
                'title' => 'Cant Find Subject',
                'detail' => trans('subject.Cant Find Subject')
            ], 404);
        }
    }
}
