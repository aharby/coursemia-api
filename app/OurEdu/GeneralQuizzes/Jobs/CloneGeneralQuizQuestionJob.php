<?php

namespace App\OurEdu\GeneralQuizzes\Jobs;

use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
use App\OurEdu\ResourceSubjectFormats\Models\QuestionHeadInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CloneGeneralQuizQuestionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected GeneralQuiz $generalQuiz, public $questionIds = [])
    {
    }

    public function handle()
    {
        foreach ($this->questionIds as $id) {
            DB::beginTransaction();
            $question_bank = GeneralQuizQuestionBank::query()->with("questions", "sections")->find($id);

            if ($question_bank->slug == QuestionsTypesEnums::DRAG_DROP_TEXT || $question_bank->slug == QuestionsTypesEnums::DRAG_DROP_IMAGE) {
                $this->cloneDragDrop($question_bank);
            }
            if ($question_bank->slug == QuestionsTypesEnums::SINGLE_CHOICE || $question_bank->slug == QuestionsTypesEnums::MULTI_CHOICE) {
                $this->cloneMultiplechoice($question_bank);
            }
            if ($question_bank->slug == QuestionsTypesEnums::TRUE_FALSE || $question_bank->slug == QuestionsTypesEnums::TRUE_FALSE_WITH_CORRECT) {
                $this->cloneTrueFalse($question_bank);
            }
            if ($question_bank->slug == QuestionsTypesEnums::ESSAY) {
                $this->cloneEssay($question_bank);
            }
            if ($question_bank->slug == QuestionsTypesEnums::COMPLETE) {
                $this->cloneComplete($question_bank);
            }
            DB::commit();
        }
    }

    private function cloneDragDrop(GeneralQuizQuestionBank $question_bank)
    {
        $questionData = $question_bank->question->replicate();
        $questionData->save();

        $oldoptions = $question_bank->question->options;
        $oldQuestion = $question_bank->question->questions->first();
        $correctOption = $oldQuestion->correct_option_id;
        $oldIndex = 0;
        $newOptions = [];
        foreach ($oldoptions as $key => $option) {
            if ($option->id == $correctOption) {
                $oldIndex = $key;
            }
            $newOption = $option->replicate();
            $newOption->res_drag_drop_data_id = $questionData->id;
            $newOption->save();
            $newOptions[] = $newOption;
        }

        $newquestion = $oldQuestion->replicate();
        $newquestion->res_drag_drop_data_id = $questionData->id;
        $newquestion->correct_option_id = $newOptions[$oldIndex]->id ?? 0;
        $newquestion->save();

        $newQuestionBank = $question_bank->replicate();
        $newQuestionBank->question_id = $questionData->id;
        $newQuestionBank->save();

        $this->generalQuiz->questions()->attach($newQuestionBank->id);

        if ($oldQuestion->video) {
            $video = $oldQuestion->video->replicate();
            $video->res_drag_drop_question_id = $newquestion->id;
            $video->save();
        }
        if ($oldQuestion->audio) {
            $audio = $oldQuestion->audio->replicate();
            $audio->res_drag_drop_question_id = $newquestion->id;
            $audio->save();
        }
        if ($oldQuestion->media) {
            $media = $oldQuestion->media->replicate();
            $media->res_drag_drop_question_id = $newquestion->id;
            $media->save();
        }
    }

    private function cloneTrueFalse(GeneralQuizQuestionBank $question_bank)
    {
        $questionData = $question_bank->question->parentData->replicate();
        $questionData->save();

        $oldQuestion = $question_bank->question;
        $question = $oldQuestion->replicate();
        $question->res_true_false_data_id = $questionData->id;
        $question->model = QuestionModelsEnums::GENERAL_QUIZ;
        $question->save();

        $newQuestionBank = $question_bank->replicate();
        $newQuestionBank->question_id = $question->id;
        $newQuestionBank->save();

        $this->generalQuiz->questions()->attach($newQuestionBank->id);

        foreach ($oldQuestion->options as $option) {
            $newOption = $option->replicate();
            $newOption->res_true_false_question_id = $question->id;
            $newOption->save();
        }

        if ($oldQuestion->video) {
            $video = $oldQuestion->video->replicate();
            $video->res_true_false_question_id = $question->id;
            $video->save();
        }
        if ($oldQuestion->audio) {
            $audio = $oldQuestion->audio->replicate();
            $audio->res_true_false_question_id = $question->id;
            $audio->save();
        }
        if ($oldQuestion->media) {
            $media = $oldQuestion->media->replicate();
            $media->res_true_false_question_id = $question->id;
            $media->save();
        }
    }

    private function cloneMultiplechoice(GeneralQuizQuestionBank $question_bank)
    {
        $questionData = $question_bank->question->parentData->replicate();
        $questionData->save();

        $oldQuestion = $question_bank->question;
        $question = $oldQuestion->replicate();
        $question->res_multiple_choice_data_id = $questionData->id;
        $question->model = QuestionModelsEnums::GENERAL_QUIZ;
        $question->save();

        $newQuestionBank = $question_bank->replicate();
        $newQuestionBank->question_id = $question->id;
        $newQuestionBank->save();

        $this->generalQuiz->questions()->attach($newQuestionBank->id);


        foreach ($oldQuestion->options as $option) {
            $newOption = $option->replicate();
            $newOption->res_multiple_choice_question_id = $question->id;
            $newOption->save();
        }

        if ($oldQuestion->video) {
            $video = $oldQuestion->video->replicate();
            $video->res_multiple_choice_question_id = $question->id;
            $video->save();
        }
        if ($oldQuestion->audio) {
            $audio = $oldQuestion->audio->replicate();
            $audio->res_multiple_choice_question_id = $question->id;
            $audio->save();
        }
        if ($oldQuestion->media) {
            $media = $oldQuestion->media->replicate();
            $media->res_multiple_choice_question_id = $question->id;
            $media->save();
        }
    }

    private function cloneComplete(GeneralQuizQuestionBank $question_bank)
    {
        $questionData = $question_bank->question->parentData->replicate();
        $questionData->save();

        $oldQuestion = $question_bank->question;
        $question = $oldQuestion->replicate();
        $question->res_complete_data_id = $questionData->id;
        $question->model = QuestionModelsEnums::GENERAL_QUIZ;
        $question->save();

        $newQuestionBank = $question_bank->replicate();
        $newQuestionBank->question_id = $question->id;
        $newQuestionBank->save();

        $this->generalQuiz->questions()->attach($newQuestionBank->id);

        if ($oldQuestion->acceptedAnswers) {
            foreach ($oldQuestion->acceptedAnswers as $answer) {
                $accept = $answer->replicate();
                $accept->res_complete_question_id = $question->id;
                $accept->save();
            }
        }
        if ($oldQuestion->answer) {
            $answer = $oldQuestion->answer->replicate();
            $answer->res_complete_question_id = $question->id;
            $answer->save();
        }
    }

    private function cloneEssay(GeneralQuizQuestionBank $question_bank)
    {
        $questionData = $question_bank->question->parentData->replicate();
        $questionData->save();

        $oldQuestion = $question_bank->question;

        $question = $oldQuestion->replicate();
        $question->res_essay_data_id = $questionData->id;
        $question->model = QuestionModelsEnums::GENERAL_QUIZ;
        $question->save();

        $newQuestionBank = $question_bank->replicate();
        $newQuestionBank->question_id = $question->id;
        $newQuestionBank->save();

        $this->generalQuiz->questions()->attach($newQuestionBank->id);

        if ($oldQuestion->video) {
            $video = $oldQuestion->video->replicate();
            $video->res_essay_question_id = $question->id;
            $video->save();
        }
        if ($oldQuestion->audio) {
            $audio = $oldQuestion->audio->replicate();
            $audio->res_essay_question_id = $question->id;
            $audio->save();
        }
        if ($oldQuestion->media) {
            $media = $oldQuestion->media->replicate();
            $media->res_essay_question_id = $question->id;
            $media->save();
        }
    }
}
