<?php

namespace Database\Seeders;

use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropOption;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingOption;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingOption;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;

use Illuminate\Database\Seeder;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteAcceptedAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;

class GenerateExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create subject
        $subject = Subject::factory()->create();

        // create formats for subject
        $subjectFormats = SubjectFormatSubject::factory()->count(3)->create(['subject_id' => $subject->id]);

        // each format
        $subjectFormats->each(function ($subjectFormat) {
            // generate questions
            $this->generateQuestions($subjectFormat);

            $child = SubjectFormatSubject::factory()->create([
                'subject_id' => $subjectFormat->subject_id,
                'parent_subject_format_id'  =>  $subjectFormat
            ]);

            $this->generateQuestions($child);
        });

        // update subject prepared questsions difficulty_level
        $subject->preparedQuestions()->chunk(5, function ($preparedQuestions) {
            $preparedQuestions->each->update([
                'difficulty_level' => Option::where(
                    'type',
                    OptionsTypes::RESOURCE_DIFFICULTY_LEVEL
                )->inRandomOrder()->first()->slug
            ]);
        });

        // dump subject id to use
        dump($subject->id);
        dump($subjectFormats->pluck('id')->toArray());
    }

    protected function generateQuestions($subjectFormat)
    {
        // create a format resource
        $subjectFormatResource = ResourceSubjectFormatSubject::factory()->create(['subject_format_subject_id' => $subjectFormat->id]);

        // create data based on format resource
        $trueFalseData = TrueFalseData::factory()->create([
            'resource_subject_format_subject_id' => $subjectFormatResource->id
        ]);

        // create true false questsions related to data
        $trueFalseQuestions = TrueFalseQuestion::factory()->count(random_int(5, 12))->create([
            'res_true_false_data_id' => $trueFalseData->id
        ]);


        // create true false options related to questions
        $trueFalseQuestions->each(function ($trueFalseQuestion) {
            // create options based on questions
            TrueFalseOption::factory()->count(2)->create([
                'res_true_false_question_id' => $trueFalseQuestion->id
            ]);
        });

        // create multi choice data
        $multiChoiceData = MultipleChoiceData::factory()->create([
            'resource_subject_format_subject_id' => $subjectFormatResource->id
        ]);

        // create questions based on data
        $multiChoiceQuestions = MultipleChoiceQuestion::factory()->count(random_int(5, 12))->create([
            'res_multiple_choice_data_id' => $multiChoiceData->id
        ]);

        $multiChoiceQuestions->each(function ($multiChoiceQuestion) {
            // create options based on questions
            MultipleChoiceOption::factory()->count(random_int(3, 5))->create([
                'res_multiple_choice_question_id' => $multiChoiceQuestion->id
            ]);
        });


        /*
         *  DRAGDROP RES
         */
        // create dragdrop question
        $dragDropDatas = DragDropData::factory()->count(random_int(5, 12))->create([
            'resource_subject_format_subject_id' => $subjectFormatResource->id
        ]);
        foreach ($dragDropDatas as $dragDropData) {
            $dragDropOptions = DragDropOption::factory()->count(3)->create([
                'res_drag_drop_data_id' => $dragDropData->id
            ]);
            foreach ($dragDropOptions as $dragDropOption) {
                DragDropQuestion::factory()->create([
                    'res_drag_drop_data_id' => $dragDropData->id,
                    'correct_option_id' => $dragDropOption->id
                ]);
            }

            //create additional option to be alone :D

            $dragDropOption = DragDropOption::factory()->create([
                'res_drag_drop_data_id' => $dragDropData->id
            ]);
        }
        /*
         *  Matching RES
         */

        $matchingDatas = MatchingData::factory()->count(random_int(5, 12))->create([
            'resource_subject_format_subject_id' => $subjectFormatResource->id
        ]);

        foreach ($matchingDatas as $matchingData) {
            $matchingQuestions = MatchingQuestion::factory()->count(3)->create([
                'res_matching_data_id' => $matchingData->id
            ]);

            foreach ($matchingQuestions as $matchingQuestion) {
                MatchingOption::factory()->create([
                    'res_matching_data_id' => $matchingData->id,
                    'res_matching_question_id' => $matchingQuestion->id
                ]);
            }
        }
        /*
         *  Multi Matching RES
         */

        $multiMatchingDatas = MultiMatchingData::factory()->count(random_int(5, 12))->create([
            'resource_subject_format_subject_id' => $subjectFormatResource->id
        ]);
        foreach ($multiMatchingDatas as $multiMatchingData) {
            $multiMatchingQuestions = MultiMatchingQuestion::factory()->count(3)->create([
                'res_multi_matching_data_id' => $multiMatchingData->id
            ]);

            $multiMatchingOptions = MultiMatchingOption::factory()->count(6)->create([
                'res_multi_matching_data_id' => $multiMatchingData->id
            ]);

            $optionsIds = $multiMatchingOptions->pluck('id')->toArray();
            $optionsOffest = 0;
            foreach ($multiMatchingQuestions as $multiMatchingQuestion) {
                $multiMatchingQuestion->options()->sync([$optionsIds[$optionsOffest], $optionsIds[$optionsOffest + 1]]);

                $optionsOffest += 2;
            }
        }
        // create data based on format resource
        $completeData = CompleteData::factory()->create([
            'resource_subject_format_subject_id' => $subjectFormatResource->id
        ]);

        // create complete questsions related to data
        $completeQuestions = CompleteQuestion::factory()->count(random_int(5, 12))->create([
            'res_complete_data_id' => $completeData->id
        ]);


        // create complete answers related to questions
        $completeQuestions->each(function ($completeQuestion) {
            // create answers based on questions
            CompleteAnswer::factory()->create([
                'res_complete_question_id' => $completeQuestion->id
            ]);

            CompleteAcceptedAnswer::factory()->count(2)->create([
                'res_complete_question_id' => $completeQuestion->id
            ]);
        });
    }
}
