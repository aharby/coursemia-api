<?php

namespace Database\Seeders;

use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use App\OurEdu\Users\User;
use Illuminate\Database\Seeder;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;

class HomeworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $instructor = User::where('username',123123000)->first();
        $subjectId = 347;
        $classroomIds = [876];
        $start_at = \Carbon\Carbon::now()->subHours(1);
        $end_at = \Carbon\Carbon::now()->addHours(12);

        $homework = GeneralQuiz::create([
            'subject_id' => $subjectId,
            'created_by'=>$instructor->id,
            'quiz_type'=>GeneralQuizTypeEnum::HOMEWORK,
            'subject_sections'=>[2180,2179,2181,2182],
            'title'=>'homework temp',
            'random_question'=>1,
            'start_at'=>$start_at,
            'end_at'=>$end_at,
            'published_at'=>now(),
            'branch_id'=>$instructor->branch_id,
            'school_account_id'=>$instructor->schoolInstructorBranch->school_account_id
        ]);
        $homework->classrooms()->sync($classroomIds);

        

        $multipleChoiceType = Option::query()->where("slug", "=", 'multiple_choice')->first();
        $multipleChoiceData = [
            'description' =>'description mcq multiple choices',
            'multiple_choice_type' => $multipleChoiceType->id ?? null,
        ];
        $multipleChoice = MultipleChoiceData::create($multipleChoiceData);

        $questionData = [
            'question' => 'mcq question 1 (multiple choice)',
            'url' =>'',
            'question_feedback' => 'feedback',
            'res_multiple_choice_data_id' => $multipleChoice->id,
            'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
            'model' => QuestionModelsEnums::GENERAL_QUIZ,
        ];        
        $multipleChoiceQuestion = MultipleChoiceQuestion::create($questionData);

        $optionData = [
            [
                'answer' => 'option 1',
                'is_correct_answer' => true,
                'res_multiple_choice_question_id' => $multipleChoiceQuestion->id,
            ],
            [
                'answer' => 'option 2',
                'is_correct_answer' => false,
                'res_multiple_choice_question_id' => $multipleChoiceQuestion->id,
            ],
            [
                'answer' => 'option 3',
                'is_correct_answer' => true,
                'res_multiple_choice_question_id' => $multipleChoiceQuestion->id,
            ],
            [
                'answer' => 'option 4',
                'is_correct_answer' => false,
                'res_multiple_choice_question_id' => $multipleChoiceQuestion->id,
            ],
        ];
        MultipleChoiceOption::insert($optionData);

        $questionBank = GeneralQuizQuestionBank::create([
            'question_type' => MultipleChoiceQuestion::class,
            'question_id' => $multipleChoiceQuestion->id,
            // 'general_quiz_id' => $homework->id,
            'school_account_branch_id'=>$instructor->branch_id,
            'school_account_id'=>$instructor->schoolInstructorBranch->school_account_id,
            'subject_format_subject_id' => 2182,
            'grade' => 5,
            'subject_id' => $subjectId
        ]);
        $homework->questions()->attach($questionBank->id);




        $TrueFalseType = Option::query()->where("slug", "=", 'true_false')->first();
        $trueFalseData = [
            'description' =>'description true and false without correct',
            'true_false_type' => $TrueFalseType->id ?? null,
        ];
        $trueOrFalse = TrueFalseData::create($trueFalseData);

        $questionData = [
            'text' => 'true false without correct question 1',
            'question_feedback' => 'feedback',
            'res_true_false_data_id' => $trueOrFalse->id,
            'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
            'model' => QuestionModelsEnums::GENERAL_QUIZ,
            'is_true'=>1
        ];        
        $trueFalseQuestion = TrueFalseQuestion::create($questionData);


        $questionBank = GeneralQuizQuestionBank::create([
            'question_type' => TrueFalseQuestion::class,
            'question_id' => $trueFalseQuestion->id,
            // 'general_quiz_id' => $homework->id,
            'school_account_branch_id'=>$instructor->branch_id,
            'school_account_id'=>$instructor->schoolInstructorBranch->school_account_id,
            'subject_format_subject_id' => 2182,
            'grade' => 5,
            'subject_id' => $subjectId
        ]);
        $homework->questions()->attach($questionBank->id);



// -----------------------------------------------------------------------------------------------------

        $TrueFalseType = Option::query()->where("slug", "=", 'true_false')->first();
        $trueFalseData = [
            'description' =>'description true and false with correct',
            'true_false_type' => $TrueFalseType->id ?? null,
        ];
        $trueOrFalse = TrueFalseData::create($trueFalseData);


        $questionData = [
            'text' => 'true false with correct question 1',
            'question_feedback' => 'feedback',
            'res_true_false_data_id' => $trueOrFalse->id,
            'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
            'model' => QuestionModelsEnums::GENERAL_QUIZ,
            'is_true'=>false
        ];        
        $trueFalseQuestion = TrueFalseQuestion::create($questionData);               

        $optionData = [
            [
                'option' => 'option 1',
                'is_correct_answer' => true,
                'res_true_false_question_id' => $trueFalseQuestion->id,
            ],
            [
                'option' => 'option 2',
                'is_correct_answer' => false,
                'res_true_false_question_id' => $trueFalseQuestion->id,
            ],
            [
                'option' => 'option 3',
                'is_correct_answer' => true,
                'res_true_false_question_id' => $trueFalseQuestion->id,
            ],
            [
                'option' => 'option 4',
                'is_correct_answer' => false,
                'res_true_false_question_id' => $trueFalseQuestion->id,
            ],
        ];


        TrueFalseOption::insert($optionData);


        $questionBank = GeneralQuizQuestionBank::create([
            'question_type' => TrueFalseQuestion::class,
            'question_id' => $trueFalseQuestion->id,
            // 'general_quiz_id' => $homework->id,
            'school_account_branch_id'=>$instructor->branch_id,
            'school_account_id'=>$instructor->schoolInstructorBranch->school_account_id,
            'subject_format_subject_id' => 2182,
            'grade' => 5,
            'subject_id' => $subjectId
        ]);

        $homework->questions()->attach($questionBank->id);

// ------------------------------------------------------------------------------------------------------

                                            // single choise 

        $multipleChoiceType = Option::query()->where("slug", "=", 'single_choice')->first();
        $multipleChoiceData = [
            'description' =>'description mcq single choice',
            'multiple_choice_type' => $multipleChoiceType->id ?? null,
        ];
        $multipleChoice = MultipleChoiceData::create($multipleChoiceData);

        $questionData = [
            'question' => 'mcq question 1 (single choice)',
            'url' =>'',
            'question_feedback' => 'feedback',
            'res_multiple_choice_data_id' => $multipleChoice->id,
            'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
            'model' => QuestionModelsEnums::GENERAL_QUIZ,
        ];        
        $multipleChoiceQuestion = MultipleChoiceQuestion::create($questionData);

        $optionData = [
            [
                'answer' => 'option 1',
                'is_correct_answer' => true,
                'res_multiple_choice_question_id' => $multipleChoiceQuestion->id,
            ],
            [
                'answer' => 'option 2',
                'is_correct_answer' => false,
                'res_multiple_choice_question_id' => $multipleChoiceQuestion->id,
            ],
            [
                'answer' => 'option 3',
                'is_correct_answer' => false,
                'res_multiple_choice_question_id' => $multipleChoiceQuestion->id,
            ],
            [
                'answer' => 'option 4',
                'is_correct_answer' => false,
                'res_multiple_choice_question_id' => $multipleChoiceQuestion->id,
            ],
        ];
        MultipleChoiceOption::insert($optionData);

        $questionBank = GeneralQuizQuestionBank::create([
            'question_type' => MultipleChoiceQuestion::class,
            'question_id' => $multipleChoiceQuestion->id,
            // 'general_quiz_id' => $homework->id,
            'school_account_branch_id'=>$instructor->branch_id,
            'school_account_id'=>$instructor->schoolInstructorBranch->school_account_id,
            'subject_format_subject_id' => 2182,
            'grade' => 5,
            'subject_id' => $subjectId
        ]);

        $homework->questions()->attach($questionBank->id);

    }
}
