<?php
namespace App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases;

use App\OurEdu\GeneralQuizzes\Enums\QuestionsPublicStatusesEnums;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
use Illuminate\Support\Facades\DB;
use App\OurEdu\Users\UserEnums;

class AddQuestionBankToGeneralQuiz implements AddQuestionBankToGeneralQuizInterface
{

    public function addQuestions(GeneralQuiz $generalQuiz, $data)
    {
        foreach ($data->questions as $questionId) {
            $question_bank = GeneralQuizQuestionBank::findOrFail($questionId);
            if ($error = $this->validateQuestion($generalQuiz, $question_bank)) {
                return $error;
            }
            if( !isset($data->operation) or $data->operation !== 'clone'){
                $generalQuiz->questions()->attach($question_bank->id, ['added_from_bank'=>true]);

            }
        }

        if (isset($data->operation) and $data->operation == 'clone') {
            if (count( $data->questions) > 5) {
                $useCase['status'] = 422;
                $useCase['detail'] = trans('general_quizzes.number_of_questions_must_be_no_more_than_5');
                $useCase['title'] = 'number_of_questions_must_be_no_more_than_5';
                return $useCase;
            }
            $this->cloneQuestions($generalQuiz, $data->questions);
        }

        $useCase['status'] = 200;
        $useCase['message'] = trans('general_quizzes.questions_added');
        return $useCase;
    }

    public function getQuestionPublicStatus(GeneralQuiz $generalQuiz, $question)
    {
        if ($question->public_status == true) {
            $permission = $generalQuiz->subject
                ->branchQuestionsPermissions
                ->where('id', $generalQuiz->branch_id)
                ->first();
            if ($permission && $permission->pivot) {
                if ($permission->pivot->school_scope == 1){
                    return QuestionsPublicStatusesEnums::SCHOOL;
                }elseif($permission->pivot->grade_scope == 1){
                    return QuestionsPublicStatusesEnums::GRADE;
                }elseif($permission->pivot->branch_scope == 1){
                    return QuestionsPublicStatusesEnums::BRANCH;
               }
            }
        }
        return QuestionsPublicStatusesEnums::PRIVATE;
    }
    private function validateQuestion(GeneralQuiz $generalQuiz, GeneralQuizQuestionBank $question_bank)
    {
        $useCase = false;

            if ($generalQuiz->questions()->wherePivot('question_id', $question_bank->id)->first()) {
                $useCase['status'] = 422;
                $useCase['detail'] = trans('general_quizzes.question_already_exists', ['id' => $question_bank->id]);
                $useCase['title'] = 'question_already_exists';
                return $useCase;
        }


        if ($question_bank->subject_id !== $generalQuiz->subject_id) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans(
                'general_quizzes.question_doesnot_belnog_to_same_subject_as_quiz',
                ['id'=>$question_bank->id,'type'=> $generalQuiz->type]
            );
            $useCase['title'] = 'question_doesnot_belnog_to_same_subject_as_quiz';
            return $useCase;
        }

        if (!in_array($question_bank->subject_format_subject_id, $generalQuiz->subject_sections)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans(
                'general_quizzes.question_doesnot_belnog_to_same_section_as_quiz',
                ['id'=>$question_bank->id,'type'=> $generalQuiz->type]
            );
            $useCase['title'] = 'question_doesnot_belnog_to_same_section_as_quiz';
            return $useCase;
        }


        return $useCase;
    }

    public function cloneQuestions (GeneralQuiz $generalQuiz , $questionIds)
    {
        foreach ($questionIds as $id) {
            DB::beginTransaction();
            $question_bank = GeneralQuizQuestionBank::query()->with("questions", "sections")->find($id);

            if ($question_bank->slug == QuestionsTypesEnums::DRAG_DROP_TEXT || $question_bank->slug == QuestionsTypesEnums::DRAG_DROP_IMAGE) {
                $this->cloneDragDrop($generalQuiz,$question_bank);
            }
            if ($question_bank->slug == QuestionsTypesEnums::SINGLE_CHOICE || $question_bank->slug == QuestionsTypesEnums::MULTI_CHOICE) {
                $this->cloneMultiplechoice($generalQuiz,$question_bank);
            }
            if ($question_bank->slug == QuestionsTypesEnums::TRUE_FALSE || $question_bank->slug == QuestionsTypesEnums::TRUE_FALSE_WITH_CORRECT) {
                $this->cloneTrueFalse($generalQuiz,$question_bank);
            }
            if ($question_bank->slug == QuestionsTypesEnums::ESSAY) {
                $this->cloneEssay($generalQuiz,$question_bank);
            }
            if ($question_bank->slug == QuestionsTypesEnums::COMPLETE) {
                $this->cloneComplete($generalQuiz,$question_bank);
            }
            DB::commit();
        }
    }

    private function cloneDragDrop(GeneralQuiz $generalQuiz ,GeneralQuizQuestionBank $question_bank)
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
        $newQuestionBank->created_by = auth()->user()->id;
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

    private function cloneTrueFalse(GeneralQuiz $generalQuiz, GeneralQuizQuestionBank $question_bank)
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
        $newQuestionBank->created_by = auth()->user()->id;
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

    private function cloneMultiplechoice(GeneralQuiz $generalQuiz, GeneralQuizQuestionBank $question_bank)
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
        $newQuestionBank->created_by = auth()->user()->id;
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

    private function cloneComplete(GeneralQuiz $generalQuiz, GeneralQuizQuestionBank $question_bank)
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
        $newQuestionBank->created_by = auth()->user()->id;
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

    private function cloneEssay(GeneralQuiz $generalQuiz, GeneralQuizQuestionBank $question_bank)
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
        $newQuestionBank->created_by = auth()->user()->id;
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

