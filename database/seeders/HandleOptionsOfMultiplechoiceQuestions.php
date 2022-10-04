<?php

namespace Database\Seeders;

use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;
use Illuminate\Database\Seeder;

class HandleOptionsOfMultiplechoiceQuestions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = MultipleChoiceQuestion::query()->whereHas(
            'prepareExamQuestion',
            function ($query) {
                $query->where('is_done', 1);
            }
        )->doesntHave('options')->get();

        foreach ($questions as $question) {
            $question->options()->withTrashed()->restore();
        }
    }
}
