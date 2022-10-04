<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateResourceFilePath extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tables = [
            'res_audio_data' => ['description'],
            'res_complete_question_data' => ['description'],
            'res_complete_questions' => ['question', 'question_feedback'],
            'res_drag_drop_data' => ['description', 'question_feedback'],
            'res_drag_drop_options' => ['option'],
            'res_essay_questions' => ['text', 'question_feedback', 'perfect_answers'],
            'res_multi_matching_data' => ['description', 'question_feedback'],
            'res_multi_matching_options' => ['option'],
            'res_multi_matching_questions' => ['text'],
            'res_multiple_choice_data' => ['description'],
            'res_multiple_choice_options' => ['answer'],
            'res_multiple_choice_questions' => ['question', 'question_feedback'],
            'res_page_data' => ['page'],
            'res_pdf_data' => ['description'],
            'res_true_false_data' => ['description'],
            'res_true_false_options' => ['option'],
            'res_true_false_questions' => ['text', 'question_feedback'],
            'res_video_data' => ['description'],
        ];
        $domains =[
            null,1,2,3,4,5,6
        ];

        foreach ($tables as $table => $cols) {
            $query='UPDATE '.$table." Set ";
            $lastCol = end($cols);
            $lastDomain = end($domains);
            foreach ($cols as $col) {
                foreach ($domains as $domain){
                    $query .="`".$col."`= REPLACE(`".$col."`,'src=\"https://admin".$domain.".ta3lom.com/storage/', 'src=\"https://abilities.s3.us-east-2.amazonaws.com/')";
                    if ($col != $lastCol || $domain != $lastDomain){
                        $query .= ' , ';
                    }
                }

            }
            $query .=" WHERE";
            foreach ($cols as $col) {
                foreach ($domains as $domain){
                    $query  .= "  `".$col."` LIKE ". "'%src=\"https://admin".$domain.".ta3lom.com/storage/%'";
                    if ($col != $lastCol || $domain != $lastDomain){
                        $query .= ' Or';
                    }
                }
            }
            DB::statement($query);
            dump($table ." updated successfully");
            }
    }
}
