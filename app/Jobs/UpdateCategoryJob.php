<?php

namespace App\Jobs;

use App\Modules\Courses\Models\Category;
use App\Modules\Courses\Models\Question;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCategoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $questions = Question::whereNull('category_id')->get();
        foreach ($questions as $question)
        {
            $category = Category::where(['course_id' => $question->course_id])->whereNotNull('parent_id')->first();
            $question->category_id = $category->id;
            $question->save();
        }
    }
}
