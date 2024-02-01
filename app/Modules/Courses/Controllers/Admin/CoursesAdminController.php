<?php

namespace App\Modules\Courses\Controllers\Admin;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\Answer;
use App\Modules\Courses\Models\Category;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseFlashcard;
use App\Modules\Courses\Models\CourseImage;
use App\Modules\Courses\Models\CourseLecture;
use App\Modules\Courses\Models\CourseNote;
use App\Modules\Courses\Models\CourseReview;
use App\Modules\Courses\Models\Question;
use App\Modules\Courses\Resources\Admin\AdminCategoriesResource;
use App\Modules\Courses\Resources\Admin\AdminUserCourseReviewResource;
use App\Modules\Courses\Resources\Admin\CategoriesResource;
use App\Modules\Courses\Resources\Admin\CoursesCollection;
use App\Modules\Courses\Resources\Admin\CoursesResource;
use App\Modules\Courses\Resources\Admin\ImagesResource;
use App\Modules\Courses\Resources\Admin\ValueTextCategoriesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Vimeo\Laravel\Facades\Vimeo;

class CoursesAdminController extends Controller
{
    public function allCourses()
    {
        $courses = Course::get();
        return customResponse(CoursesResource::collection($courses), "Done", 200, StatusCodesEnum::DONE);
    }

    public function getCourseImages(){
        $images = CourseImage::where('course_id', \request()->course_id)->get();
        return customResponse(ImagesResource::collection($images), '', 200, StatusCodesEnum::DONE);
    }

    public function index(Request $request)
    {
        $courses = Course::query();
        if (isset($request->speciality)) {
            $courses = $courses->where('speciality_id', $request->speciality);
        }
        if (isset($request->status)) {
            $courses = $courses->where('is_active', $request->status);
        }
        if (isset($request->admin_id)) {
            $courses = $courses->where('admin_id', $request->admin_id);
        }
        $courses = $courses->sorter();
        $courses = $courses->with('admin')->paginate(request()->perPage, ['*'], 'page', request()->page);
        return response()->json([
            'total' => $courses->total(),
            'courses' => CoursesResource::collection($courses->items())
        ]);
    }

    public function show($id)
    {
        $course = Course::find($id);
        return customResponse(new CoursesResource($course), "Done", 200, StatusCodesEnum::DONE);
    }

    public function update(Request $request, $id)
    {
        $course = Course::find($id);
        if ($request->price_after_discount >= $request->price){
            return customResponse(null, "Price after discount can't be greater than or equal price", 422, StatusCodesEnum::FAILED);
        }
        if ($request->has('is_active')){
            $course->is_active = $request->is_active;
        }
        if ($request->has('title_en')) {
            $course->title_en = $request->title_en;
        }
        if ($request->has('title_ar')) {
            $course->title_ar = $request->title_ar;
        }
        if ($request->has('description_en')) {
            $course->description_en = $request->description_en;
        }
        if ($request->has('description_ar')) {
            $course->description_ar = $request->description_ar;
        }
        if ($request->has('price')) {
            $course->price = $request->price;
        }
        if ($request->has('price_after_discount')){
            $course->price_after_discount = $request->price_after_discount;
        }
        if ($request->has('expire_date')){
            $course->expire_date = $request->expire_date;
        }
        if ($request->has('expire_duration')){
            $course->expire_duration = $request->expire_duration;
        }
        if ($request->has('speciality_id')){
            $course->speciality_id = $request->speciality_id;
        }
        if ($request->has('image')){
            $course->cover_image = moveSingleGarbageMediaToPublic($request->get('image'), 'courses');
        }
        $course->save();
        return customResponse(null, "Updated successfully", 200, StatusCodesEnum::DONE);
    }

    public function destroy($id)
    {
        Course::where('id', $id)->delete();
        return customResponse(null, "Deleted successfully", 200, StatusCodesEnum::DONE);
    }

    public function store(Request $request)
    {
        $course = new Course();
        if (isset($request->price_after_discount) && $request->price_after_discount >= $request->price){
            return customResponse(null, "Price after discount can't be greater than or equal price", 422, StatusCodesEnum::FAILED);
        }
        if ($request->has('is_active')){
            $course->is_active = $request->is_active;
        }
        if ($request->has('title_en')) {
            $course->title_en = $request->title_en;
        }
        if ($request->has('title_ar')) {
            $course->title_ar = $request->title_ar;
        }
        if ($request->has('description_en')) {
            $course->description_en = $request->description_en;
        }
        if ($request->has('description_ar')) {
            $course->description_ar = $request->description_ar;
        }
        if ($request->has('price')) {
            $course->price = $request->price;
        }
        if ($request->has('price_after_discount')) {
            $course->price_after_discount = $request->price_after_discount;
        }
        if ($request->has('expire_date') && isset($request->expire_date)) {
            $course->expire_date = $request->expire_date;
        }
        if ($request->has('expire_duration') && isset($request->expire_duration)) {
            $course->expire_duration = $request->expire_duration;
        }
        if ($request->has('speciality_id')) {
            $course->speciality_id = $request->speciality_id;
        }
        if ($request->has('cover_image')) {
            $course->cover_image = moveSingleGarbageMediaToPublic($request->get('cover_image'), 'courses');
        }
        $course->admin_id = auth('admin')->user()->id;
        $course->save();
        return customResponse(new CoursesResource($course), "Course added successfully, you can continue adding course data or add them later", 200, StatusCodesEnum::DONE);
    }

    public function storeCourseCategories(Request $request)
    {
        $categories = $request->categories;
        foreach ($categories as $category) {
            $course_category = new Category;
            $course_category->title_en = $category['title_en'];
            $course_category->title_ar = $category['title_ar'];
            $course_category->course_id = $request->course_id;
            $course_category->save();
            foreach ($category['subs'] as $sub){
                $subCategory = new Category;
                $subCategory->title_en = $sub['title_en'];
                $subCategory->title_ar = $sub['title_ar'];
                $subCategory->parent_id = $course_category->id;
                $subCategory->course_id = $course_category->course_id;
                $subCategory->save();
            }
        }
        $categories = Category::where('course_id', $request->course_id)->get();
        return customResponse(ValueTextCategoriesResource::collection($categories), "Categories added successfully", 200, StatusCodesEnum::DONE);
    }

    public function storeCourseFlashCards(Request $request)
    {
        $course_id = $request->course_id;
        $cards = $request->cards;
        $flashCards = [];
        foreach ($cards as $card) {
            $flash = new CourseFlashcard;
            $flash->course_id = $course_id;
            $flash->category_id = $card['sub_category_id'] ?? $card['category_id'];
            $flash->is_free_content = $card['is_free_content'];
            $flash->{'back:en'} = $card['back_en'];
            $flash->{'back:ar'} = $card['back_ar'];
            $flash->{'front:en'} = $card['front_en'];
            $flash->{'front:ar'} = $card['front_ar'];
            $flash->save();
        }
        return customResponse((object)[], "Flashcards added successfully", 200, StatusCodesEnum::DONE);
    }

    public function storeCourseNotes(Request $request)
    {
        $course_id = $request->course_id;
        $notes = $request->notes;
        foreach ($notes as $note) {
            $courseNote = new CourseNote;
            $courseNote->{'title:en'} = $note['title_en'];
            if (isset($note['title_ar']))
                $courseNote->{'title:ar'} = $note['title_ar'];
            $courseNote->is_free_content = $note['is_free_content'];
            $courseNote->category_id = $note['sub_category_id'] ?? $note['category_id'];
            $courseNote->course_id = $course_id;
            $courseNote->url = $note['path'];
            $courseNote->save();
        }
        return customResponse((object)[], "Notes added successfully", 200, StatusCodesEnum::DONE);
    }

    public function storeCourseLectures(Request $request)
    {
        $course_id = $request->course_id;
        $lectures = $request->lectures;
        foreach ($lectures as $lecture) {
            $courseLecture = new CourseLecture;
            $courseLecture->course_id = $course_id;
            $courseLecture->category_id = $lecture['sub_category_id'] ?? $lecture['category_id'];
            $courseLecture->title_en = $lecture['title_en'];
            $courseLecture->title_ar = $lecture['title_ar'];
            $courseLecture->description_en = $lecture['description_en'];
            $courseLecture->description_ar = $lecture['description_ar'];
            $courseLecture->is_free_content = $lecture['is_free_content'];
            $courseLecture->url = $lecture['path'];
            $courseLecture->video_thumb = moveSingleGarbageMediaToPublic($lecture['thumb'], 'courses');
            $courseLecture->save();
        }
        return customResponse((object)[], "Notes added successfully", 200, StatusCodesEnum::DONE);
    }

    public function uploadToVimeo(Request $request)
    {
        $uri = Vimeo::upload($request->file);
        return response()->json([
            'url' => 'https://player.vimeo.com' . str_replace('videos', 'video', $uri)
        ]);
    }

    public function storeCourseImages(Request $request)
    {
        $course_id = $request->course_id;
        $images = $request->images;
        foreach ($images as $image) {
            $courseImage = new CourseImage;
            $courseImage->course_id = $course_id;
            $courseImage->image = moveSingleGarbageMediaToPublic($image, 'courses');
            $courseImage->save();
        }
        return customResponse((object)[], "Images added successfully", 200, StatusCodesEnum::DONE);
    }

    public function deleteCourseImage(Request $request){
        $image = CourseImage::where('course_id', $request->course_id)->skip($request->image)->first();
        $image->delete();
        return customResponse([], '', 200, StatusCodesEnum::DONE);
    }

    public function storeSingleCourseImages(Request $request)
    {
        $course_id = $request->course_id;
        $images = $request->images;
//        CourseImage::where('course_id', $course_id)->delete();
        foreach ($images as $image) {
            $courseImage = new CourseImage;
            $courseImage->course_id = $course_id;
            $courseImage->image = moveSingleGarbageMediaToPublic($image['id'], 'courses');
            $courseImage->save();
        }
        return customResponse((object)[], "Images added successfully", 200, StatusCodesEnum::DONE);
    }

    public function storeQuestionsAndAnswers(Request $request)
    {
        $course_id = $request->course_id;
        $questions = $request->questions;
        foreach ($questions as $question) {
            $courseQuestion = new Question;
            $courseQuestion->course_id = $course_id;
            $courseQuestion->category_id = $question['sub_category_id'] ?? $question['category_id'];
            $courseQuestion->{'title:en'} = $question['title_en'];
            $courseQuestion->{'title:ar'} = $question['title_ar'];
            $courseQuestion->{'explanation:en'} = $question['explanation']['explanation_en'];
            $courseQuestion->{'explanation:ar'} = $question['explanation']['explanation_ar'];
            if (isset($question['image']))
                $courseQuestion->image = moveSingleGarbageMediaToPublic($question['image'], 'courses');
            $courseQuestion->explanation_image = moveSingleGarbageMediaToPublic($question['explanation']['image'], 'courses');
            $courseQuestion->explanation_voice = $question['explanation']['voice_path'];
            $courseQuestion->is_free_content = $question['is_free_content'];
            $courseQuestion->save();
            $answers = $question['answers'];
            foreach ($answers as $answer) {
                $questionAnswer = new Answer;
                $questionAnswer->question_id = $courseQuestion->id;
                $questionAnswer->{'answer:en'} = $answer['answer_en'];
                if (isset($answer['answer_ar'])){
                    $questionAnswer->{'answer:ar'} = $answer['answer_ar'];
                }else{
                    $questionAnswer->{'answer:ar'} = null;
                }
                $questionAnswer->is_correct = $answer['is_correct'];
                $questionAnswer->chosen_percentage = 0;
                $questionAnswer->save();
            }
        }
        return customResponse((object)[], "Questions & Answers submitted successfully", 200, StatusCodesEnum::DONE);
    }

    public function uploadPdf(Request $request)
    {
        $pdf = $request->file;
        $fileExtension = trim($pdf->getClientOriginalExtension());
        if (!isset($fileExtension) || $fileExtension == '' || $fileExtension == '      '){
            $fileExtension = 'mp3';
        }
        $fileName = strtolower(Str::random(10).trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $pdf->getClientOriginalName()), '-')) . '.' . $fileExtension;
        $location = 'public/uploads/documents';
        $path = $pdf->storeAs(
            $location, $fileName
        );
        return response()->json(['path' => 'storage' . '/' . $path]);
    }

    public function getCourseCategories()
    {
        $categories = Category::query();
        $categories = $categories->where('course_id', \request()->course_id)->whereNull('parent_id');
        $categories = $categories->get();
        return customResponse(ValueTextCategoriesResource::collection($categories), "Categories added successfully", 200, StatusCodesEnum::DONE);
    }

    public function getCourseSubCategories()
    {
        $categories = Category::where('course_id', \request()->course_id)->where('parent_id', \request()->category_id)->get();
        return customResponse(ValueTextCategoriesResource::collection($categories), "Categories added successfully", 200, StatusCodesEnum::DONE);
    }

    public function getCourseCategoriesList($id){
        $sortBy = \request()->sortBy;
        $sortDesc = \request()->sortDesc;
        if ($sortDesc == 'true'){
            $sortDir = 'DESC';
        }else{
            $sortDir = 'ASC';
        }
        $categories = Category::query();
        $categories = $categories->filter()->sorter();
        $categories = $categories->where('course_id', $id);
        if (isset($sortBy) && $sortBy == 'title_en'){
            $categories = $categories->orderBy('title_en', $sortDir);
        }
        $is_sub = \request()->is_sub;
        if (isset($is_sub) && $is_sub == "true"){
            $categories = $categories->whereNotNull('parent_id');
        }else{
            $categories = $categories->whereNull('parent_id');
        }
        $categories = $categories->paginate(request()->perPage, ['*'], 'page', request()->page);
        return response()->json([
            'total' => $categories->total(),
            'categories' => AdminCategoriesResource::collection($categories->items())
        ]);
    }

    public function getAllCategories(){
        $sortBy = \request()->sortBy;
        $sortDesc = \request()->sortDesc;
        if ($sortDesc == 'true'){
            $sortDir = 'DESC';
        }else{
            $sortDir = 'ASC';
        }
        $categories = Category::query();
        $categories = $categories->filter()->sorter();
        if (isset($sortBy) && $sortBy == 'title_en'){
            $categories = $categories->orderBy('title_en', $sortDir);
        }
        $categories->whereNotNull('parent_id');
        $categories = $categories->paginate(request()->perPage, ['*'], 'page', request()->page);
        return response()->json([
            'total' => $categories->total(),
            'categories' => AdminCategoriesResource::collection($categories->items())
        ]);
    }

    public function showCategory($id){
        $category = Category::where('id', $id)->first();
        return customResponse(new AdminCategoriesResource($category), "Category deleted successfully", 200, StatusCodesEnum::DONE);
    }

    public function updateCategory($id){
        $category = Category::where('id', $id)->first();
        $category->title_ar = \request()->title_ar;
        $category->title_en = \request()->title_en;
        $category->save();
        $subs = \request()->subs;
        $subCats = [];
        foreach ($subs as $sub){
            if (isset($sub['id'])){
                $subCat = Category::find($sub['id']);
                $subCat->title_en = $sub['title_en'];
                $subCat->title_ar = $sub['title_ar'];
                $subCat->save();
                array_push($subCats, $sub['id']);
            }else{
                $subCat = new Category;
                $subCat->title_en = $sub['title_en'];
                $subCat->title_ar = $sub['title_ar'];
                $subCat->course_id = $category->course_id;
                $subCat->parent_id = $id;
                $subCat->save();
                array_push($subCats, $subCat->id);
            }
        }
        Category::where('parent_id', $id)->whereNotIn('id', $subCats)->delete();
        return customResponse(new AdminCategoriesResource($category), "Category updated successfully", 200, StatusCodesEnum::DONE);
    }

    public function addCategory($id){
        $category = new Category;
        $category->course_id = $id;
        $category->title_ar = \request()->title_ar;
        $category->title_en = \request()->title_en;
        $parent_category = request()->parent_category;
        if (isset($parent_category) && $parent_category > 0){
            $category->parent_id = $parent_category;
        }
        $category->save();
        return customResponse(new AdminCategoriesResource($category), "Category added successfully", 200, StatusCodesEnum::DONE);
    }

    public function deleteCourseCategory($id){
        Category::where('id', $id)->delete();
        return customResponse((object)[], "Category deleted successfully", 200, StatusCodesEnum::DONE);
    }

    public function getCourseReviews($id){
        $sortBy = \request()->sortBy;
        $sortDesc = \request()->sortDesc;
        if ($sortDesc == 'true'){
            $sortDir = 'DESC';
        }else{
            $sortDir = 'ASC';
        }
        $reviews = CourseReview::query();
        $reviews = $reviews->filter();
        $reviews = $reviews->where('course_id', $id);
        if (isset($sortBy) && $sortBy == 'rate'){
            $reviews = $reviews->orderBy('rate', $sortDir);
        }
        if (isset($sortBy) && $sortBy == 'date'){
            $reviews = $reviews->orderBy('created_at', $sortDir);
        }
        $reviews = $reviews->paginate(request()->perPage, ['*'], 'page', request()->page);
        return response()->json([
            'total' => $reviews->total(),
            'reviews' => AdminUserCourseReviewResource::collection($reviews->items())
        ]);
    }

    public function deleteCourseReview($id){
        CourseReview::where('id', $id)->delete();
        return customResponse((object)[], "Review deleted successfully", 200, StatusCodesEnum::DONE);
    }

    public function delete($id){
        Course::where('id', $id)->delete();
        return customResponse((object)[], "Course deleted successfully", 200, StatusCodesEnum::DONE);
    }

    public function getAllCategoriesNoPagination(){
        $categories = Category::whereNull('parent_id')->get();
        return customResponse(CategoriesResource::collection($categories), "Done", 200, StatusCodesEnum::DONE);
    }

    public function getCourseByCategoryId(Request $request)
    {
        $category = Category::findOrFail($request->category_id);
        return customResponse(new CategoriesResource($category), '', 200, StatusCodesEnum::DONE);
    }
}
