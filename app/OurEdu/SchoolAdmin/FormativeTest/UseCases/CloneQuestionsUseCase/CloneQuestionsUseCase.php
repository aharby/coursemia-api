<?php

namespace App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CloneQuestionsUseCase;

use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
use App\OurEdu\Users\User;
use Illuminate\Support\Facades\DB;

class CloneQuestionsUseCase implements CloneQuestionsUseCaseInterface
{
    public function clone (GeneralQuiz $generalQuiz , $questions, User $creator = null)
    {
        foreach ($questions as $question_bank) {
            if ($question_bank->slug == QuestionsTypesEnums::DRAG_DROP_TEXT || $question_bank->slug == QuestionsTypesEnums::DRAG_DROP_IMAGE) {
                $this->cloneDragDrop($generalQuiz, $question_bank, $creator);
            }
            if ($question_bank->slug == QuestionsTypesEnums::SINGLE_CHOICE || $question_bank->slug == QuestionsTypesEnums::MULTI_CHOICE) {
                $this->cloneMultiplechoice($generalQuiz, $question_bank, $creator);
            }
            if ($question_bank->slug == QuestionsTypesEnums::TRUE_FALSE || $question_bank->slug == QuestionsTypesEnums::TRUE_FALSE_WITH_CORRECT) {
                $this->cloneTrueFalse($generalQuiz, $question_bank, $creator);
            }
            if ($question_bank->slug == QuestionsTypesEnums::ESSAY) {
                $this->cloneEssay($generalQuiz, $question_bank, $creator);
            }
            if ($question_bank->slug == QuestionsTypesEnums::COMPLETE) {
                $this->cloneComplete($generalQuiz, $question_bank, $creator);
            }
        }
    }

    private function cloneDragDrop(GeneralQuiz $generalQuiz ,GeneralQuizQuestionBank $question_bank, User $creator = null)
    {
        $questionBankQuestion = $question_bank->question ?? $question_bank->questions;

        $questionData = $questionBankQuestion->replicate();
        $questionData->save();

        $oldoptions = $questionBankQuestion->options;
        $oldQuestion =$questionBankQuestion->questions->first();
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
        $newQuestionBank->subject_id = $generalQuiz->subject_id;
        $newQuestionBank->subject_format_subject_id = count($generalQuiz->sections) ? $generalQuiz->sections->random()->id : null;
        $newQuestionBank->save();

        $generalQuiz->questions()->attach($newQuestionBank->id);

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

    private function cloneTrueFalse(GeneralQuiz $generalQuiz, GeneralQuizQuestionBank $question_bank, User $creator = null)
    {
        $questionBankQuestion = $question_bank->question ?? $question_bank->questions;

        $questionData = $questionBankQuestion->parentData->replicate();
        $questionData->save();

        $oldQuestion =$questionBankQuestion;
        $question = $oldQuestion->replicate();
        $question->res_true_false_data_id = $questionData->id;
        $question->model = QuestionModelsEnums::GENERAL_QUIZ;
        $question->save();

        $newQuestionBank = $question_bank->replicate();
        $newQuestionBank->question_id = $question->id;
        $newQuestionBank->subject_id = $generalQuiz->subject_id;
        $newQuestionBank->subject_format_subject_id = count($generalQuiz->sections) ? $generalQuiz->sections->random()->id : null;
        $newQuestionBank->save();

        $generalQuiz->questions()->attach($newQuestionBank->id);

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

    private function cloneMultiplechoice(GeneralQuiz $generalQuiz, GeneralQuizQuestionBank $question_bank, User $creator = null)
    {
        $questionBankQuestion = $question_bank->question ?? $question_bank->questions;

        $questionData = $questionBankQuestion->parentData->replicate();
        $questionData->save();

        $oldQuestion = $questionBankQuestion;
        $question = $oldQuestion->replicate();
        $question->res_multiple_choice_data_id = $questionData->id;
        $question->model = QuestionModelsEnums::GENERAL_QUIZ;
        $question->save();

        $newQuestionBank = $question_bank->replicate();
        $newQuestionBank->question_id = $question->id;
        $newQuestionBank->subject_id = $generalQuiz->subject_id;
        $newQuestionBank->subject_format_subject_id = count($generalQuiz->sections) ? $generalQuiz->sections->random()->id : null;
        $newQuestionBank->save();

        $generalQuiz->questions()->attach($newQuestionBank->id);


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

    private function cloneComplete(GeneralQuiz $generalQuiz, GeneralQuizQuestionBank $question_bank, User $creator = null)
    {
        $questionBankQuestion = $question_bank->question ?? $question_bank->questions;

        $questionData = $questionBankQuestion->parentData->replicate();
        $questionData->save();

        $oldQuestion = $questionBankQuestion;
        $question = $oldQuestion->replicate();
        $question->res_complete_data_id = $questionData->id;
        $question->model = QuestionModelsEnums::GENERAL_QUIZ;
        $question->save();

        $newQuestionBank = $question_bank->replicate();
        $newQuestionBank->question_id = $question->id;
        $newQuestionBank->subject_id = $generalQuiz->subject_id;
        $newQuestionBank->subject_format_subject_id = count($generalQuiz->sections) ? $generalQuiz->sections->random()->id : null;
        $newQuestionBank->save();

        $generalQuiz->questions()->attach($newQuestionBank->id);

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

    private function cloneEssay(GeneralQuiz $generalQuiz, GeneralQuizQuestionBank $question_bank, User $creator = null)
    {
        $questionBankQuestion = $question_bank->question ?? $question_bank->questions;

        $questionData = $questionBankQuestion->parentData->replicate();
        $questionData->save();

        $oldQuestion = $questionBankQuestion;

        $question = $oldQuestion->replicate();
        $question->res_essay_data_id = $questionData->id;
        $question->model = QuestionModelsEnums::GENERAL_QUIZ;
        $question->save();

        $newQuestionBank = $question_bank->replicate();
        $newQuestionBank->question_id = $question->id;
        $newQuestionBank->subject_id = $generalQuiz->subject_id;
        $newQuestionBank->subject_format_subject_id = count($generalQuiz->sections) ? $generalQuiz->sections->random()->id : null;
        $newQuestionBank->save();

        $generalQuiz->questions()->attach($newQuestionBank->id);

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
