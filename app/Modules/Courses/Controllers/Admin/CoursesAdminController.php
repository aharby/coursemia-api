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
use App\Modules\Courses\Resources\Admin\AdminUserCourseReviewResource;
use App\Modules\Courses\Resources\Admin\CategoriesResource;
use App\Modules\Courses\Resources\Admin\CoursesCollection;
use App\Modules\Courses\Resources\Admin\CoursesResource;
use App\Modules\Courses\Resources\Admin\ValueTextCategoriesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Vimeo\Laravel\Facades\Vimeo;

class CoursesAdminController extends Controller
{
    public function allCourses(){
        $courses = Course::get();
        return customResponse(CoursesResource::collection($courses), "Done", 200, StatusCodesEnum::DONE);
    }
    public function index(Request $request){
        $courses = Course::query();
        if (isset($request->speciality)){
            $courses = $courses->where('speciality_id', $request->speciality);
        }
        if (isset($request->status)){
            $courses = $courses->where('is_active', $request->status);
        }
        $courses = $courses->sorter();
        $courses = $courses->paginate(request()->perPage, ['*'], 'page', request()->page);
        return response()->json([
            'total' => $courses->total(),
            'courses' => CoursesResource::collection($courses->items())
        ]);
    }

    public function show($id){
        $course = Course::find($id);
        return customResponse(new CoursesResource($course), "Done", 200, StatusCodesEnum::DONE);
    }

    public function update(Request $request, $id){
        $course = Course::find($id);
        if ($request->price_after_discount >= $request->price){
            return customResponse(null, "Price after discount can't be greater than or equal price", 422, StatusCodesEnum::FAILED);
        }
        if ($request->has('is_active')){
            $course->is_active = $request->is_active;
        }
        if ($request->has('title_en')){
            $course->title_en = $request->title_en;
        }
        if ($request->has('title_ar')){
            $course->title_ar = $request->title_ar;
        }
        if ($request->has('description_en')){
            $course->description_en = $request->description_en;
        }
        if ($request->has('description_ar')){
            $course->description_ar = $request->description_ar;
        }
        if ($request->has('price')){
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

    public function destroy($id){
        Course::where('id', $id)->delete();
        return customResponse(null, "Deleted successfully", 200, StatusCodesEnum::DONE);
    }

    public function store(Request $request){
        $course = new Course();
        if (isset($request->price_after_discount) && $request->price_after_discount >= $request->price){
            return customResponse(null, "Price after discount can't be greater than or equal price", 422, StatusCodesEnum::FAILED);
        }
        if ($request->has('is_active')){
            $course->is_active = $request->is_active;
        }
        if ($request->has('title_en')){
            $course->title_en = $request->title_en;
        }
        if ($request->has('title_ar')){
            $course->title_ar = $request->title_ar;
        }
        if ($request->has('description_en')){
            $course->description_en = $request->description_en;
        }
        if ($request->has('description_ar')){
            $course->description_ar = $request->description_ar;
        }
        if ($request->has('price')){
            $course->price = $request->price;
        }
        if ($request->has('price_after_discount')){
            $course->price_after_discount = $request->price_after_discount;
        }
        if ($request->has('expire_date') && isset($request->expire_date)){
            $course->expire_date = $request->expire_date;
        }
        if ($request->has('expire_duration') && isset($request->expire_duration)){
            $course->expire_duration = $request->expire_duration;
        }
        if ($request->has('speciality_id')){
            $course->speciality_id = $request->speciality_id;
        }
        if ($request->has('cover_image')){
            $course->cover_image = moveSingleGarbageMediaToPublic($request->get('cover_image'), 'courses');
        }
        $course->save();
        return customResponse(new CoursesResource($course), "Course added successfully, you can continue adding course data or add them later", 200, StatusCodesEnum::DONE);
    }

    public function storeCourseCategories(Request $request){
        $categories = $request->categories;
        foreach ($categories as $category){
            $course_category = new Category;
            $course_category->title_en = $category['title_en'];
            $course_category->title_ar = $category['title_ar'];
            $course_category->course_id = $request->course_id;
            $course_category->save();
        }
        $categories = Category::where('course_id', $request->course_id)->get();
        return customResponse(ValueTextCategoriesResource::collection($categories), "Categories added successfully", 200, StatusCodesEnum::DONE);
    }

    public function storeCourseFlashCards(Request $request){
        $course_id = $request->course_id;
        $cards = $request->cards;
        foreach ($cards as $card){
            $flashCard = new CourseFlashcard;
            $flashCard->course_id = $course_id;
            $flashCard->front_en = $card['front_en'];
            $flashCard->front_ar = $card['front_ar'];
            $flashCard->back_en = $card['back_en'];
            $flashCard->back_ar = $card['back_ar'];
            $flashCard->category_id = $card['category_id'];
            $flashCard->is_free_content = $card['is_free_content'];
            $flashCard->save();
        }
        return customResponse((object)[], "Flashcards added successfully", 200, StatusCodesEnum::DONE);
    }

    public function storeCourseNotes(Request $request){
        $course_id = $request->course_id;
        $notes = $request->notes;
        foreach ($notes as $note){
            $courseNote = new CourseNote;
            $courseNote->{'title:en'} = $note['title_en'];
            $courseNote->{'title:ar'} = $note['title_ar'];
            $courseNote->is_free_content = $note['is_free_content'];
            $courseNote->category_id = $note['category_id'];
            $courseNote->course_id = $course_id;
            $courseNote->url = $note['path'];
            $courseNote->save();
        }
        return customResponse((object)[], "Notes added successfully", 200, StatusCodesEnum::DONE);
    }

    public function storeCourseLectures(Request $request){
        $course_id = $request->course_id;
        $lectures = $request->lectures;
        foreach ($lectures as $lecture){
            $courseLecture = new CourseLecture;
            $courseLecture->course_id = $course_id;
            $courseLecture->category_id = $lecture['category_id'];
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

    public function uploadToVimeo(Request $request){
        $uri = Vimeo::upload($request->file);
        return response()->json([
            'url' => 'https://player.vimeo.com'.str_replace('videos', 'video', $uri)
        ]);
    }

    public function storeCourseImages(Request $request){
        $course_id = $request->course_id;
        $images = $request->images;
        foreach ($images as $image){
            $courseImage = new CourseImage;
            $courseImage->course_id = $course_id;
            $courseImage->image = moveSingleGarbageMediaToPublic($image, 'courses');
            $courseImage->save();
        }
        return customResponse((object)[], "Images added successfully", 200, StatusCodesEnum::DONE);
    }

    public function storeQuestionsAndAnswers(Request $request){
        $course_id = $request->course_id;
        $questions = $request->questions;
        foreach ($questions as $question){
            $courseQuestion = new Question;
            $courseQuestion->course_id = $course_id;
            $courseQuestion->category_id = $question['category_id'];
            $courseQuestion->{'title:en'} = $question['title_en'];
            $courseQuestion->{'title:ar'} = $question['title_ar'];
            $courseQuestion->{'explanation:en'} = $question['explanation']['explanation_en'];
            $courseQuestion->{'explanation:ar'} = $question['explanation']['explanation_ar'];
            if (isset($question['image']))
                $courseQuestion->image = moveSingleGarbageMediaToPublic($question['image'], 'courses');
            $courseQuestion->explanation_image = moveSingleGarbageMediaToPublic($question['explanation']['image_path'], 'courses');
            $courseQuestion->explanation_voice = moveSingleGarbageMediaToPublic($question['explanation']['voice_path'], 'courses');
            $courseQuestion->is_free_content = $question['is_free_content'];
            $courseQuestion->save();
            $answers = $question['answers'];
            foreach ($answers as $answer){
                $questionAnswer = new Answer;
                $questionAnswer->question_id = $courseQuestion->id;
                $questionAnswer->{'answer:en'} = $answer['answer_en'];
                $questionAnswer->{'answer:ar'} = $answer['answer_ar'];
                $questionAnswer->is_correct = $answer['is_correct'];
                $questionAnswer->chosen_percentage = 0;
                $questionAnswer->save();
            }
        }
        return customResponse((object)[], "Questions & Answers submitted successfully", 200, StatusCodesEnum::DONE);
    }

    public function uploadPdf(Request $request){
        $pdf = $request->file;
        $fileExtension = $pdf->getClientOriginalExtension();
        $fileName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $pdf->getClientOriginalName()), '-')). '.'. $fileExtension;
        $location = 'public/uploads/documents';
        $path = $pdf->storeAs(
            $location, $fileName
        );
        return response()->json(['path' => 'storage'.'/'.$path]);
    }

    public function getCourseCategories(){
        $categories = Category::where('course_id', \request()->course_id)->get();
        return customResponse(ValueTextCategoriesResource::collection($categories), "Categories added successfully", 200, StatusCodesEnum::DONE);
    }

    public function getCourseReviews($id){
        $reviews = CourseReview::where('course_id', $id)
            ->paginate(request()->perPage, ['*'], 'page', request()->page);
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
}
