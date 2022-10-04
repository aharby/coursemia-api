<?php

namespace App\OurEdu\SchoolAdmin\FormativeTest\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AjaxController extends BaseController
{
    public function getEducationalSystemGradeClasses(Request $request)
    {
        $request->validate(
            [
                'educational_system_id' => 'required'
            ]
        );

        $authUSer = Auth::user();
        $schoolsAccountsIDs = $authUSer->schoolAdminAssignedSchools()->pluck("school_accounts.id")->toArray();

        $gradeClasses = GradeClass::query()
            ->with('translations')
            ->whereHas(
                "schoolAccounts",
                function (Builder $schoolAccountQBuilder) use ($schoolsAccountsIDs) {
                    $schoolAccountQBuilder->whereIn("id", $schoolsAccountsIDs);
                }
            )
            ->get()
            ->where("educational_system_id", "=", $request->get("educational_system_id"))
            ->pluck("title", "id")
            ->toArray();

        return response()->json(
            [
                "status" => 200,
                'gradeClasses' => $gradeClasses
            ]
        );
    }

    public function getSubjectsByGrades(Request $request)
    {
        $request->validate(
            [
                'grade_class_id' => "required"
            ]
        );

        $subjects = Subject::query()
            ->where("grade_class_id", $request->get("grade_class_id"))
            ->distinct()
            ->pluck('name', 'id')
            ->toArray();

        return response()->json(
            [
                "status" => 200,
                'subjects' => $subjects
            ]
        );
    }

    public function getSubjectMainSections(Subject $subject)
    {
        $sections = $subject
            ->subjectFormatSubject()
            ->where('parent_subject_format_id', "=", null)
            ->pluck('title', 'id')
            ->toArray();


        return response()->json(
            [
                "status" => 200,
                'sections' => $sections
            ]
        );
    }
}
