<?php


namespace App\OurEdu\GradeColors\Admin\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\GradeColors\Admin\AssignGradesRequest;
use App\OurEdu\GradeColors\Models\GradeColor;

class GradeColorController extends BaseController
{

    /**
     * @var string
     */
    private $module;
    /**
     * @var array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|mixed|string|null
     */
    private $title;
    /**
     * @var string
     */
    private $parent;

    public function __construct() {
        $this->module = 'grade_colors';
        $this->title = trans('app.Grade Color');
        $this->parent = ParentEnum::ADMIN;
    }


    public function index()
    {
        $data['rows'] = GradeColor::query()->paginate(env("PAGE_LIMIT", 20));
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.Grade Color');
        $data['breadcrumb'] = [trans('app.Grade Color') => route('admin.grade.colors.index')];

        return view($this->parent . '.' . $this->module . '.index',  $data);
    }

    public function assignGrade(GradeColor $gradeColor)
    {
        $data['rows'] = GradeClass::all();
        $gradeClasses = $gradeColor->gradeClasses()->select("id")->get();

        $selectedRows = [];
        foreach ($gradeClasses as $class) {
            $selectedRows[] = $class->id;
        }

        $data['selectedRows'] =$selectedRows;
        $data['row'] = $gradeColor;
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';

        return view($this->parent . '.' . $this->module . '.assign_grades',  $data);
    }

    public function postAssignGrade(AssignGradesRequest $request, GradeColor $gradeColor)
    {

        GradeClass::query()
            ->where("grade_color_id", "=", $gradeColor->id)
            ->update(["grade_color_id"=> null]);

        if ($request->filled("color_grades")) {
            GradeClass::query()
                ->whereIn("id", $request->get("color_grades"))
                ->update(["grade_color_id"=> $gradeColor->id]);
        }

        flash("success assigned");
        return redirect()->route("admin.grade.colors.index");
    }
}
