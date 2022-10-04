<?php

namespace App\OurEdu\BaseApp\Providers;

use App\OurEdu\Assessments\AssessmentManager\UseCases\ViewAsAssessorUseCase\ViewAsAssessorUseCase;
use App\OurEdu\Assessments\AssessmentManager\UseCases\ViewAsAssessorUseCase\ViewAsAssessorUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\EssayQuestionUseCase\EssayQuestionPostAnswerUseCase;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\EssayQuestionUseCase\EssayQuestionPostAnswerUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\MatrixUseCase\MatrixPostAnswerUseCase;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\MatrixUseCase\MatrixPostAnswerUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\MultipleChoiceUseCase\MultipleChoicePostAnswerUseCase as AssessmentMultipleChoicePostAnswerUseCase;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\MultipleChoiceUseCase\MultipleChoicePostAnswerUseCaseInterface as AssessmentMultipleChoicePostAnswerUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCase as AssessmentPostAnswerUseCase;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCaseInterface as AssessmentPostAnswerUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\SatisfactionUseCase\SatisfactionPostAnswerUseCase;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\SatisfactionUseCase\SatisfactionPostAnswerUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\ScaleRatingUseCase\ScaleRatingPostAnswer;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\ScaleRatingUseCase\ScaleRatingPostAnswerInterface;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\StarRatingUseCase\StarRatingPostAnswerUseCase;
use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\StarRatingUseCase\StarRatingPostAnswerUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\FinishAssessmentUseCase\FinishAssessmentUseCase;
use App\OurEdu\Assessments\Assessor\UseCases\FinishAssessmentUseCase\FinishAssessmentUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\NextAndBack\AssessmentNextBackUseCase;
use App\OurEdu\Assessments\Assessor\UseCases\NextAndBack\AssessmentNextBackUseCaseInterface;
use App\OurEdu\Assessments\Assessor\UseCases\StartAssessmentUseCase\StartAssessmentUseCase;
use App\OurEdu\Assessments\Assessor\UseCases\StartAssessmentUseCase\StartAssessmentUseCaseInterface;
use App\OurEdu\Assessments\UseCases\AssessmentPointsRate\AssessmentPointRateUseCase;
use App\OurEdu\Assessments\UseCases\AssessmentPointsRate\AssessmentPointRateUseCaseInterface;
use App\OurEdu\Assessments\UseCases\AssessmentQuestionUseCase\AssessmentQuestionUseCase;
use App\OurEdu\Assessments\UseCases\AssessmentQuestionUseCase\AssessmentQuestionUseCaseInterface;
use App\OurEdu\Assessments\UseCases\CloneAssessment\CloneAssessmentUseCase;
use App\OurEdu\Assessments\UseCases\CloneAssessment\CloneAssessmentUseCaseInterface;
use App\OurEdu\Assessments\UseCases\CreateAssessmentUseCase\CreateAssessmentUseCase;
use App\OurEdu\Assessments\UseCases\CreateAssessmentUseCase\CreateAssessmentUseCaseInterface;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\EssayUseCase\EssayUseCase;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\EssayUseCase\EssayUseCaseInterface;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\MatrixUseCase\MatrixUseCase;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\MatrixUseCase\MatrixUseCaseInterface;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\MultipleChoiceUseCase\MultipleChoiceUseCase as AssessmentMultipleChoiceUseCase;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\MultipleChoiceUseCase\MultipleChoiceUseCaseInterface as AssessmentMultipleChoiceUseCaseInterface;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\SatisficationRatingUseCase\SatisficationRatingUseCase;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\SatisficationRatingUseCase\SatisficationRatingUseCaseInterface;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\ScaleRatingUseCase\ScaleRatingUseCase;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\ScaleRatingUseCase\ScaleRatingUseCaseInterface;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\StarRatingUseCase\StarRatingUseCase;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\StarRatingUseCase\StarRatingUseCaseInterface;
use App\OurEdu\Assessments\UseCases\UpdateAssessmentUseCase\UpdateAssessmentUseCase;
use App\OurEdu\Assessments\UseCases\UpdateAssessmentUseCase\UpdateAssessmentUseCaseInterface;
use App\OurEdu\Certificates\UseCases\ThankingCertificatesUseCase;
use App\OurEdu\Certificates\UseCases\ThankingCertificatesUseCaseInterface;
use App\OurEdu\Courses\UseCases\CourseMediaUseCase\CourseMediaUseCase;
use App\OurEdu\Courses\UseCases\CourseMediaUseCase\CourseMediaUseCaseInterface;
use App\OurEdu\Courses\UseCases\CourseRateUseCase\CourseRateUseCase;
use App\OurEdu\Courses\UseCases\CourseRateUseCase\CourseRateUseCaseInterface;
use App\OurEdu\Courses\UseCases\CourseSubscribeUseCase\V2\CourseSubscribeUseCase;
use App\OurEdu\Courses\UseCases\CourseSubscribeUseCase\V2\CourseSubscribeUseCaseInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\CompletePostAnswerUseCase\CompletePostAnswerUseCase;
use App\OurEdu\Exams\UseCases\AnswerUseCase\CompletePostAnswerUseCase\CompletePostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\DragDropPostAnswerUseCase\DragDropPostAnswerUseCase;
use App\OurEdu\Exams\UseCases\AnswerUseCase\DragDropPostAnswerUseCase\DragDropPostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\HotSpotPostAnswerUseCase\HotSpotPostAnswerUseCase;
use App\OurEdu\Exams\UseCases\AnswerUseCase\HotSpotPostAnswerUseCase\HotSpotPostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\MatchingPostAnswerUseCase\MatchingPostAnswerUseCase;
use App\OurEdu\Exams\UseCases\AnswerUseCase\MatchingPostAnswerUseCase\MatchingPostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\MultiChoicePostAnswerUseCase\MultiChoicePostAnswerUseCase;
use App\OurEdu\Exams\UseCases\AnswerUseCase\MultiChoicePostAnswerUseCase\MultiChoicePostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\MultipleMatchingPostAnswerUseCase\MultipleMatchingPostAnswerUseCase;
use App\OurEdu\Exams\UseCases\AnswerUseCase\MultipleMatchingPostAnswerUseCase\MultipleMatchingPostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCase;
use App\OurEdu\Exams\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\TrueFalsePostAnswerUseCase\TrueFalsePostAnswerUseCase;
use App\OurEdu\Exams\UseCases\AnswerUseCase\TrueFalsePostAnswerUseCase\TrueFalsePostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\ExamChallengeUseCase\ExamChallengeUseCase;
use App\OurEdu\Exams\UseCases\ExamChallengeUseCase\ExamChallengeUseCaseInterface;
use App\OurEdu\Exams\UseCases\ExamTakeLikeUseCase\ExamTakeLikeUseCase;
use App\OurEdu\Exams\UseCases\ExamTakeLikeUseCase\ExamTakeLikeUseCaseInterface;
use App\OurEdu\Exams\UseCases\FinishExamUseCase\FinishExamUseCase;
use App\OurEdu\Exams\UseCases\FinishExamUseCase\FinishExamUseCaseInterface;
use App\OurEdu\Exams\UseCases\HandelExamQuestionTimeUseCase\HandelExamQuestionTimeUseCase;
use App\OurEdu\Exams\UseCases\HandelExamQuestionTimeUseCase\HandelExamQuestionTimeUseCaseInterface;
use App\OurEdu\Exams\UseCases\NextBackUseCase\NextBackUseCase;
use App\OurEdu\Exams\UseCases\NextBackUseCase\NextBackUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\CreateUpdatePrepareQuestionUseCase\CreateUpdatePrepareQuestionUseCase;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\CreateUpdatePrepareQuestionUseCase\CreateUpdatePrepareQuestionUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase\GenerateExamUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase\V2\GenerateExamUseCase;
use App\OurEdu\Exams\UseCases\RequestLiveSessionUseCase\RequestLiveSessionUseCase;
use App\OurEdu\Exams\UseCases\RequestLiveSessionUseCase\RequestLiveSessionUseCaseInterface;
use App\OurEdu\Exams\UseCases\StartExamUseCase\StartExamUseCase;
use App\OurEdu\Exams\UseCases\StartExamUseCase\StartExamUseCaseInterface;
use App\OurEdu\GeneralExamReport\UseCases\ReportGeneralExamReportUseCase\ReportGeneralExamReportUseCase;
use App\OurEdu\GeneralExamReport\UseCases\ReportGeneralExamReportUseCase\ReportGeneralExamReportUseCaseInterface;
use App\OurEdu\GeneralExams\UseCases\FinishGeneralExamUseCase\FinishGeneralExamUseCase;
use App\OurEdu\GeneralExams\UseCases\FinishGeneralExamUseCase\FinishGeneralExamUseCaseInterface;
use App\OurEdu\GeneralExams\UseCases\GeneralExam\GeneralExamUseCase;
use App\OurEdu\GeneralExams\UseCases\GeneralExam\GeneralExamUseCaseInterface;
use App\OurEdu\GeneralExams\UseCases\NextAndBack\GeneralExamNextBackUseCase;
use App\OurEdu\GeneralExams\UseCases\NextAndBack\GeneralExamNextBackUseCaseInterface;
use App\OurEdu\GeneralExams\UseCases\PreparedQuestions\PreperedGeneralExamQuestionUseCase;
use App\OurEdu\GeneralExams\UseCases\PreparedQuestions\PreperedGeneralExamQuestionUseCaseInterface;
use App\OurEdu\GeneralExams\UseCases\StartExamUseCase\StartGeneralExamUseCase;
use App\OurEdu\GeneralExams\UseCases\StartExamUseCase\StartGeneralExamUseCaseInterface;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\UseCases\CreateCourseHomeworkUsecase;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\UseCases\CreateCourseHomeworkUseCaseInterface;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\UseCases\UpdateCourseHomeworkUseCase;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\UseCases\UpdateCourseHomeworkUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\UseCases\InstructorHomeworkUseCases\CreateHomeworkUseCase;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\UseCases\InstructorHomeworkUseCases\CreateHomeworkUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\UseCases\InstructorHomeworkUseCases\UpdateHomeworkUseCase;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\UseCases\InstructorHomeworkUseCases\UpdateHomeworkUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Homework\UseCases\HomeworkUseCase as GeneralQuizHomeworkUseCase;
use App\OurEdu\GeneralQuizzes\Homework\UseCases\HomeworkUseCaseInterface as GeneralQuizHomeworkUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Homework\UseCases\RetakeGeneralQuizUseCase;
use App\OurEdu\GeneralQuizzes\Homework\UseCases\RetakeGeneralQuizUseCaseInterface;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\UseCases\CreatePeriodicTestUseCase\CreatePeriodicTestUseCase;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\UseCases\CreatePeriodicTestUseCase\CreatePeriodicTestUseCaseInterface;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\UseCases\UpdatePeriodicTestUseCase\UpdatePeriodicTestUseCase;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\UseCases\UpdatePeriodicTestUseCase\UpdatePeriodicTestUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\EssayPostAnswerUseCase\EssayPostAnswerUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\EssayPostAnswerUseCase\EssayPostAnswerUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\SingleChoicePostAnswerUseCase\SingleChoicePostAnswerUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\SingleChoicePostAnswerUseCase\SingleChoicePostAnswerUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\TrueFalsePostAnswerWithCorrectUseCase\TrueFalsePostAnswerWithCorrectUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\TrueFalsePostAnswerWithCorrectUseCase\TrueFalsePostAnswerWithCorrectUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\FinishGeneralQuizUseCase\FinishGeneralQuizUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\FinishGeneralQuizUseCase\FinishGeneralQuizUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\GeneralUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\GeneralUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\AddQuestionBankToGeneralQuiz;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\AddQuestionBankToGeneralQuizInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\CompleteQuestionUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\CompleteQuestionUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralEssayCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralEssayUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralQuizDragDropUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralQuizDragDropUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralQuizMultipleChoiceUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralQuizMultipleChoiceUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralQuizQuestionUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralQuizQuestionUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralTrueFalseUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralTrueFalseUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\TrueFalseQuestionWithCorrectUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\TrueFalseQuestionWithCorrectUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\NextAndBack\GeneralQuizNextBackUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\NextAndBack\GeneralQuizNextBackUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\StartGeneralQuizUseCase\StartGeneralQuizUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\StartGeneralQuizUseCase\StartGeneralQuizUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\StudentPeriodicTestTimeUseCase\StudentPeriodicTestTimeUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\StudentPeriodicTestTimeUseCase\StudentPeriodicTestTimeUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\ViewAsStudentUseCase\ViewAsStudentUseCase;
use App\OurEdu\GeneralQuizzes\UseCases\ViewAsStudentUseCase\ViewAsStudentUseCaseInterface;
use App\OurEdu\Invitations\UseCases\SuperviseInvitationUseCase;
use App\OurEdu\Invitations\UseCases\SuperviseInvitationUseCaseInterface;
use App\OurEdu\LearningPerformance\UseCases\StudentOrderUseCase\StudentOrderUseCase;
use App\OurEdu\LearningPerformance\UseCases\StudentOrderUseCase\StudentOrderUseCaseInterface;
use App\OurEdu\LearningPerformance\UseCases\StudentSuccessRateUseCase\StudentSuccessRateUseCase;
use App\OurEdu\LearningPerformance\UseCases\StudentSuccessRateUseCase\StudentSuccessRateUseCaseInterface;
use App\OurEdu\Payments\UseCases\SubmitTransactionUseCase;
use App\OurEdu\Payments\UseCases\SubmitTransactionUseCaseInterface;
use App\OurEdu\PsychologicalTests\UseCases\PsychologicalTestUseCase;
use App\OurEdu\PsychologicalTests\UseCases\PsychologicalTestUseCaseInterface;
use App\OurEdu\QuestionReport\UseCases\GetQuestionReportTasks\GetQuestionReportTasksUseCase;
use App\OurEdu\QuestionReport\UseCases\GetQuestionReportTasks\GetQuestionReportTasksUseCaseInterface;
use App\OurEdu\QuestionReport\UseCases\MarkQuestionReportTaskAsDoneUseCase\MarkQuestionReportTaskAsDoneUseCase;
use App\OurEdu\QuestionReport\UseCases\MarkQuestionReportTaskAsDoneUseCase\MarkQuestionReportTaskAsDoneUseCaseInterface;
use App\OurEdu\QuestionReport\UseCases\PullQuestionReportTasks\PullQuestionReportTasksUseCase;
use App\OurEdu\QuestionReport\UseCases\PullQuestionReportTasks\PullQuestionReportTasksUseCaseInterface;
use App\OurEdu\QuestionReport\UseCases\ReportQuestionReportUseCase\ReportQuestionReportUseCase;
use App\OurEdu\QuestionReport\UseCases\ReportQuestionReportUseCase\ReportQuestionReportUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\AnswerQuizQuestionUseCase\AnswerQuizQuestionUseCase;
use App\OurEdu\Quizzes\UseCases\AnswerQuizQuestionUseCase\AnswerQuizQuestionUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\FinishQuizUseCase\FinishQuizUseCase;
use App\OurEdu\Quizzes\UseCases\FinishQuizUseCase\FinishQuizUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\HomeWorkUseCase\HomeWorkUseCase;
use App\OurEdu\Quizzes\UseCases\HomeWorkUseCase\HomeWorkUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\QuizQuestionUseCase\QuizQuestionUseCase;
use App\OurEdu\Quizzes\UseCases\QuizQuestionUseCase\QuizQuestionUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\QuizUseCase\QuizUseCase;
use App\OurEdu\Quizzes\UseCases\QuizUseCase\QuizUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\StartQuizUseCase\StartQuizUseCase;
use App\OurEdu\Quizzes\UseCases\StartQuizUseCase\StartQuizUseCaseInterface;
use App\OurEdu\Reports\UseCase\SMEListReportsUseCase\SMEListReportsUseCase;
use App\OurEdu\Reports\UseCase\SMEListReportsUseCase\SMEListReportsUseCaseInterface;
use App\OurEdu\Reports\UseCase\StudentReportUseCase\StudentReportUseCase;
use App\OurEdu\Reports\UseCase\StudentReportUseCase\StudentReportUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\AudioUseCase\FillAudioUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\AudioUseCase\FillAudioUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\CompleteUseCase\FillCompleteUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\CompleteUseCase\FillCompleteUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\DragDropUseCase\FillDragDropUseCaseUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\DragDropUseCase\FillDragDropUseCaseUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\FillResourceUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\FillResourceUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\FlashUseCase\FillFlashUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\FlashUseCase\FillFlashUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\HotSpotUseCase\FillHotSpotUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\HotSpotUseCase\FillHotSpotUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MatchingUseCase\FillMatchingUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MatchingUseCase\FillMatchingUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MultiMatchingUseCase\FillMultiMatchingUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MultiMatchingUseCase\FillMultiMatchingUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MultipleChoiceUseCase\FillMultipleChoiceUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MultipleChoiceUseCase\FillMultipleChoiceUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PageUseCase\FillPageUseCaseUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PageUseCase\FillPageUseCaseUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PdfUseCase\FillPdfUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PdfUseCase\FillPdfUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PictureUseCase\FillPictureUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PictureUseCase\FillPictureUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\TrueFalseUseCase\FillTrueFalseUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\TrueFalseUseCase\FillTrueFalseUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\VideoUseCase\FillVideoUseCase;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\VideoUseCase\FillVideoUseCaseInterface;
use App\OurEdu\SchoolAccounts\ClassroomClass\Repositories\ImportJobRepository;
use App\OurEdu\SchoolAccounts\ClassroomClass\Repositories\ImportJobRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\UseCases\Imports\ImportJobsUseCase;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\UseCases\Imports\ImportJobsUseCaseInterface;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\UseCases\ClassroomClassSessionUseCase;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\UseCases\ClassroomClassSessionUseCaseInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\UseCases\SchoolAccountBranchUseCase\SchoolAccountBranchUseCase;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\UseCases\SchoolAccountBranchUseCase\SchoolAccountBranchUseCaseInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\UseCases\users\SchoolUsersUseCase;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\UseCases\users\SchoolUsersUseCaseInterface;
use App\OurEdu\SchoolAccounts\SchoolAccounts\Admin\UseCases\SchoolAccountUseCases\SchoolAccountUseCase;
use App\OurEdu\SchoolAccounts\SchoolAccounts\Admin\UseCases\SchoolAccountUseCases\SchoolAccountUseCaseInterface;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\UseCases\EducationalSupervisorUseCase;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\UseCases\EducationalSupervisorUseCaseInterface;
use App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\SessionPreparationUseCase\SessionPreparationUseCase;
use App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\SessionPreparationUseCase\SessionPreparationUseCaseInterface;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CloneFormativeUseCase\CloneFormativeUseCase;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CloneFormativeUseCase\CloneFormativeUseCaseInterface;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CloneQuestionsUseCase\CloneQuestionsUseCase;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CloneQuestionsUseCase\CloneQuestionsUseCaseInterface;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CreateFormativeTestUseCase\CreateFormativeTestUseCase;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CreateFormativeTestUseCase\CreateFormativeTestUseCaseInterface;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\UpdateFormativeTestUseCase\UpdateFormativeTestUseCase;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\UpdateFormativeTestUseCase\UpdateFormativeTestUseCaseInterface;
use App\OurEdu\SubjectPackages\UseCases\StudentSubscribeUseCase\StudentSubscribeUseCase;
use App\OurEdu\SubjectPackages\UseCases\StudentSubscribeUseCase\StudentSubscribeUseCaseInterface;
use App\OurEdu\Subjects\UseCases\EditResource\EditResourceSubjectFormatSubjectUseCase;
use App\OurEdu\Subjects\UseCases\EditResource\EditResourceSubjectFormatSubjectUseCaseInterface;
use App\OurEdu\Subjects\UseCases\GetTasks\GetTasksUseCase;
use App\OurEdu\Subjects\UseCases\GetTasks\GetTasksUseCaseInterface;
use App\OurEdu\Subjects\UseCases\MarkTaskAsDoneUseCase\MarkTaskAsDoneUseCase;
use App\OurEdu\Subjects\UseCases\MarkTaskAsDoneUseCase\MarkTaskAsDoneUseCaseInterface;
use App\OurEdu\Subjects\UseCases\NotifyParentsAboutSubjectProgressUseCase\NotifyParentsAboutSubjectProgressUseCase;
use App\OurEdu\Subjects\UseCases\NotifyParentsAboutSubjectProgressUseCase\NotifyParentsAboutSubjectProgressUseCaseInterface;
use App\OurEdu\Subjects\UseCases\PullTaskUseCase\PullTaskUseCase;
use App\OurEdu\Subjects\UseCases\PullTaskUseCase\PullTaskUseCaseInterface;
use App\OurEdu\Subjects\UseCases\ReleaseTaskUseCase\ReleaseTaskUseCase;
use App\OurEdu\Subjects\UseCases\ReleaseTaskUseCase\ReleaseTaskUseCaseInterface;
use App\OurEdu\Subjects\UseCases\SubjectStructural\GenerateTasksUseCase\GenerateTasksUseCase;
use App\OurEdu\Subjects\UseCases\SubjectStructural\GenerateTasksUseCase\GenerateTasksUseCaseInterface;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase\UpdateSubjectStructuralUseCase;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase\UpdateSubjectStructuralUseCaseInterface;
use App\OurEdu\Subjects\UseCases\SubscribeUseCase\V2\SubscribeUseCase;
use App\OurEdu\Subjects\UseCases\SubscribeUseCase\V2\SubscribeUseCaseInterface;
use App\OurEdu\Subjects\UseCases\UpdateProgressUseCase\UpdateProgressUseCase;
use App\OurEdu\Subjects\UseCases\UpdateProgressUseCase\UpdateProgressUseCaseInterface;
use App\OurEdu\Subscribes\UseCases\SubscriptionUseCase;
use App\OurEdu\Subscribes\UseCases\SubscriptionUseCaseInterface;
use App\OurEdu\Users\Auth\TokenManager\PassportTokenManager;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\Repository\SchoolAdminRepository;
use App\OurEdu\Users\Repository\SchoolAdminRepositoryInterface;
use App\OurEdu\Users\UseCases\ActivateUserUserCase\ActivateUserUseCase;
use App\OurEdu\Users\UseCases\ActivateUserUserCase\ActivateUserUseCaseInterface;
use App\OurEdu\Users\UseCases\CreateContentAuthorUseCase\CreateContentAuthorUseCase;
use App\OurEdu\Users\UseCases\CreateContentAuthorUseCase\CreateContentAuthorUseCaseInterface;
use App\OurEdu\Users\UseCases\CreateInstructorUseCase\CreateInstructorUseCase;
use App\OurEdu\Users\UseCases\CreateInstructorUseCase\CreateInstructorUseCaseInterface;
use App\OurEdu\Users\UseCases\CreateSchoolAdminUseCase\CreateSchoolAdminUseCase;
use App\OurEdu\Users\UseCases\CreateSchoolAdminUseCase\CreateSchoolAdminUseCaseInterface;
use App\OurEdu\Users\UseCases\CreateStudentTeacherUseCase\CreateStudentTeacherUseCase;
use App\OurEdu\Users\UseCases\CreateStudentTeacherUseCase\CreateStudentTeacherUseCaseInterface;
use App\OurEdu\Users\UseCases\CreateStudentUseCase\CreateStudentUseCase;
use App\OurEdu\Users\UseCases\CreateStudentUseCase\CreateStudentUseCaseInterface;
use App\OurEdu\Users\UseCases\CreateUserUseCase\CreateUserUseCase;
use App\OurEdu\Users\UseCases\CreateUserUseCase\CreateUserUseCaseInterface;
use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCase;
use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface;
use App\OurEdu\Users\UseCases\ForgetPasswordUseCase\ForgetPasswordUseCase;
use App\OurEdu\Users\UseCases\ForgetPasswordUseCase\ForgetPasswordUseCaseInterface;
use App\OurEdu\Users\UseCases\LoginUseCase\LoginUseCase;
use App\OurEdu\Users\UseCases\LoginUseCase\LoginUseCaseInterface;
use App\OurEdu\Users\UseCases\RegisterStudentTeacherUseCase\RegisterStudentTeacherUseCase;
use App\OurEdu\Users\UseCases\RegisterStudentTeacherUseCase\RegisterStudentTeacherUseCaseInterface;
use App\OurEdu\Users\UseCases\RegisterStudentUseCase\RegisterStudentUseCase;
use App\OurEdu\Users\UseCases\RegisterStudentUseCase\RegisterStudentUseCaseInterface;
use App\OurEdu\Users\UseCases\RegisterUseCase\RegisterUseCase;
use App\OurEdu\Users\UseCases\RegisterUseCase\RegisterUseCaseInterface;
use App\OurEdu\Users\UseCases\ResetSchoolInstructorPasswordUseCase\ResetSchoolInstructorPasswordUseCase;
use App\OurEdu\Users\UseCases\ResetSchoolInstructorPasswordUseCase\ResetSchoolInstructorPasswordUseCaseInterface;
use App\OurEdu\Users\UseCases\SendActivationMailUseCase\SendActivationMailUseCase;
use App\OurEdu\Users\UseCases\SendActivationMailUseCase\SendActivationMailUseCaseInterface;
use App\OurEdu\Users\UseCases\SendActivationSmsUseCase\SendActivationSmsUseCase;
use App\OurEdu\Users\UseCases\SendActivationSmsUseCase\SendActivationSmsUseCaseInterface;
use App\OurEdu\Users\UseCases\SendLoginOtp\SendLoginOtp;
use App\OurEdu\Users\UseCases\SendLoginOtp\SendLoginOtpImp;
use App\OurEdu\Users\UseCases\SuspendUserUseCase\SuspendUserUseCase;
use App\OurEdu\Users\UseCases\SuspendUserUseCase\SuspendUserUseCaseInterface;
use App\OurEdu\Users\UseCases\TwitterStatelessUseCase\TwitterStatelessUseCase;
use App\OurEdu\Users\UseCases\TwitterStatelessUseCase\TwitterStatelessUseCaseInterface;
use App\OurEdu\Users\UseCases\UpdateContentAuthorUserCase\UpdateContentAuthorUseCase;
use App\OurEdu\Users\UseCases\UpdateContentAuthorUserCase\UpdateContentAuthorUseCaseInterface;
use App\OurEdu\Users\UseCases\UpdateInstructorUseCase\UpdateInstructorUseCase;
use App\OurEdu\Users\UseCases\UpdateInstructorUseCase\UpdateInstructorUseCaseInterface;
use App\OurEdu\Users\UseCases\UpdateProfileUseCase\UpdateProfileUseCase;
use App\OurEdu\Users\UseCases\UpdateProfileUseCase\UpdateProfileUseCaseInterface;
use App\OurEdu\Users\UseCases\UpdateSchoolAdminUseCase\UpdateSchoolAdminUseCase;
use App\OurEdu\Users\UseCases\UpdateSchoolAdminUseCase\UpdateSchoolAdminUseCaseInterface;
use App\OurEdu\Users\UseCases\UpdateStudentUseCase\UpdateStudentUseCase;
use App\OurEdu\Users\UseCases\UpdateStudentUseCase\UpdateStudentUseCaseInterface;
use App\OurEdu\Users\UseCases\UpdateUserUseCase\UpdateUserUseCase;
use App\OurEdu\Users\UseCases\UpdateUserUseCase\UpdateUserUseCaseInterface;
use App\OurEdu\VCRSchedules\UseCases\VCRNotificationUseCase\VCRNotificationUseCase;
use App\OurEdu\VCRSchedules\UseCases\VCRNotificationUseCase\VCRNotificationUseCaseInterface;
use App\OurEdu\VCRSchedules\UseCases\VCRRequestUseCase\VCRRequestUseCase;
use App\OurEdu\VCRSchedules\UseCases\VCRRequestUseCase\VCRRequestUseCaseInterface;
use App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase\VCRSessionUseCase;
use App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase\VCRSessionUseCaseInterface;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\GetVCRSessionUseCase;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\GetVCRSessionUseCaseInterface;
use App\OurEdu\VideoCall\UseCases\GetAgoraTokenUseCase\GenerateTokenInterface;
use App\OurEdu\VideoCall\UseCases\GetAgoraTokenUseCase\GenerateTokenUseCase;
use App\OurEdu\VideoCall\UseCases\VideoCallUseCase\VideoCallUseCase;
use App\OurEdu\VideoCall\UseCases\VideoCallUseCase\VideoCallUseCaseInterface;
use Illuminate\Support\ServiceProvider;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use Swis\JsonApi\Client\Parsers\DocumentParser;

class UseCasesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ParserInterface::class, DocumentParser::class);
        $this->app->bind(TokenManagerInterface::class, PassportTokenManager::class);
        $this->app->bind(
            \App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCaseInterface::class,
            \App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCase::class
        );

        $this->app->bind(
            SingleChoicePostAnswerUseCaseInterface::class,
            SingleChoicePostAnswerUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\MultiChoicePostAnswerUseCase\MultiChoicePostAnswerUseCaseInterface::class,
            \App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\MultiChoicePostAnswerUseCase\MultiChoicePostAnswerUseCase::class
        );

        $this->app->bind(
            TrueFalsePostAnswerWithCorrectUseCaseInterface::class,
            TrueFalsePostAnswerWithCorrectUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\TrueFalsePostAnswerUseCase\TrueFalsePostAnswerUseCaseInterface::class,
            \App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\TrueFalsePostAnswerUseCase\TrueFalsePostAnswerUseCase::class
        );


        $this->app->bind(
            FinishGeneralQuizUseCaseInterface::class,
            FinishGeneralQuizUseCase::class
        );

        $this->app->bind(
            GeneralQuizNextBackUseCaseInterface::class,
            GeneralQuizNextBackUseCase::class
        );


        $this->app->bind(
            ClassroomClassSessionUseCaseInterface::class,
            ClassroomClassSessionUseCase::class
        );

        $this->app->bind(
            EducationalSupervisorUseCaseInterface::class,
            EducationalSupervisorUseCase::class
        );
        $this->app->bind(
            LoginUseCaseInterface::class,
            LoginUseCase::class
        );

        $this->app->bind(
            CreateUserUseCaseInterface::class,
            CreateUserUseCase::class
        );

        $this->app->bind(
            UpdateUserUseCaseInterface::class,
            UpdateUserUseCase::class
        );

        $this->app->bind(
            CreateContentAuthorUseCaseInterface::class,
            CreateContentAuthorUseCase::class
        );

        $this->app->bind(
            UpdateContentAuthorUseCaseInterface::class,
            UpdateContentAuthorUseCase::class
        );

        $this->app->bind(
            CreateInstructorUseCaseInterface::class,
            CreateInstructorUseCase::class
        );

        $this->app->bind(
            UpdateInstructorUseCaseInterface::class,
            UpdateInstructorUseCase::class
        );

        $this->app->bind(
            ForgetPasswordUseCaseInterface::class,
            ForgetPasswordUseCase::class
        );

        $this->app->bind(
            UpdateSubjectStructuralUseCaseInterface::class,
            UpdateSubjectStructuralUseCase::class
        );

        $this->app->bind(
            SubmitTransactionUseCaseInterface::class,
            SubmitTransactionUseCase::class
        );

        $this->app->bind(
            GenerateTasksUseCaseInterface::class,
            GenerateTasksUseCase::class
        );

        $this->app->bind(
            SuspendUserUseCaseInterface::class,
            SuspendUserUseCase::class
        );

        $this->app->bind(
            UpdateProfileUseCaseInterface::class,
            UpdateProfileUseCase::class
        );

        $this->app->bind(
            GetTasksUseCaseInterface::class,
            GetTasksUseCase::class
        );

        $this->app->bind(
            PullTaskUseCaseInterface::class,
            PullTaskUseCase::class
        );

        $this->app->bind(
            FillResourceUseCaseInterface::class,
            FillResourceUseCase::class
        );

        $this->app->bind(
            FillTrueFalseUseCaseInterface::class,
            FillTrueFalseUseCase::class
        );
        $this->app->bind(
            FillVideoUseCaseInterface::class,
            FillVideoUseCase::class
        );
        $this->app->bind(
            RegisterUseCaseInterface::class,
            RegisterUseCase::class
        );
        $this->app->bind(
            SendActivationMailUseCaseInterface::class,
            SendActivationMailUseCase::class
        );
        $this->app->bind(
            ActivateUserUseCaseInterface::class,
            ActivateUserUseCase::class
        );
        $this->app->bind(
            FillMultiMatchingUseCaseInterface::class,
            FillMultiMatchingUseCase::class
        );
        $this->app->bind(
            FillMatchingUseCaseInterface::class,
            FillMatchingUseCase::class
        );

        $this->app->bind(
            FillDragDropUseCaseUseCaseInterface::class,
            FillDragDropUseCaseUseCase::class
        );

        $this->app->bind(
            FillPageUseCaseUseCaseInterface::class,
            FillPageUseCaseUseCase::class
        );

        $this->app->bind(
            FillAudioUseCaseInterface::class,
            FillAudioUseCase::class
        );

        $this->app->bind(
            FillFlashUseCaseInterface::class,
            FillFlashUseCase::class
        );

        $this->app->bind(
            FillPdfUseCaseInterface::class,
            FillPdfUseCase::class
        );
        $this->app->bind(
            FillMultipleChoiceUseCaseInterface::class,
            FillMultipleChoiceUseCase::class
        );
        $this->app->bind(
            FillPictureUseCaseInterface::class,
            FillPictureUseCase::class
        );
        $this->app->bind(
            CreateUpdatePrepareQuestionUseCaseInterface::class,
            CreateUpdatePrepareQuestionUseCase::class
        );

        $this->app->bind(
            RegisterStudentUseCaseInterface::class,
            RegisterStudentUseCase::class
        );

        $this->app->bind(
            SubscribeUseCaseInterface::class,
            SubscribeUseCase::class
        );

        $this->app->bind(
            SubscriptionUseCaseInterface::class,
            SubscriptionUseCase::class
        );

        $this->app->bind(
            PreperedGeneralExamQuestionUseCaseInterface::class,
            PreperedGeneralExamQuestionUseCase::class
        );

        $this->app->bind(
            StudentReportUseCaseInterface::class,
            StudentReportUseCase::class
        );

        $this->app->bind(
            PsychologicalTestUseCaseInterface::class,
            PsychologicalTestUseCase::class
        );

        $this->app->bind(
            GenerateExamUseCaseInterface::class,
            GenerateExamUseCase::class
        );

        $this->app->bind(
            StartExamUseCaseInterface::class,
            StartExamUseCase::class
        );
        $this->app->bind(
            FinishExamUseCaseInterface::class,
            FinishExamUseCase::class
        );

        $this->app->bind(
            NextBackUseCaseInterface::class,
            NextBackUseCase::class
        );
        $this->app->bind(
            PostAnswerUseCaseInterface::class,
            PostAnswerUseCase::class
        );

        $this->app->bind(
            TrueFalsePostAnswerUseCaseInterface::class,
            TrueFalsePostAnswerUseCase::class
        );

        $this->app->bind(
            MultiChoicePostAnswerUseCaseInterface::class,
            MultiChoicePostAnswerUseCase::class
        );

        $this->app->bind(
            HotSpotPostAnswerUseCaseInterface::class,
            HotSpotPostAnswerUseCase::class
        );

        $this->app->bind(
            MarkTaskAsDoneUseCaseInterface::class,
            MarkTaskAsDoneUseCase::class
        );

        $this->app->bind(
            DragDropPostAnswerUseCaseInterface::class,
            DragDropPostAnswerUseCase::class
        );

        $this->app->bind(
            MatchingPostAnswerUseCaseInterface::class,
            MatchingPostAnswerUseCase::class
        );

        $this->app->bind(
            MultipleMatchingPostAnswerUseCaseInterface::class,
            MultipleMatchingPostAnswerUseCase::class
        );

        $this->app->bind(
            FillCompleteUseCaseInterface::class,
            FillCompleteUseCase::class
        );

        $this->app->bind(
            HandelExamQuestionTimeUseCaseInterface::class,
            HandelExamQuestionTimeUseCase::class
        );

        $this->app->bind(
            SuperviseInvitationUseCaseInterface::class,
            SuperviseInvitationUseCase::class
        );

        // $this->app->bind(
        //     NotifyParentsAboutExamResultUseCaseInterface::class,
        //     NotifyParentsAboutExamResultUseCase::class
        // );

        $this->app->bind(
            UpdateProgressUseCaseInterface::class,
            UpdateProgressUseCase::class
        );

        $this->app->bind(
            CompletePostAnswerUseCaseInterface::class,
            CompletePostAnswerUseCase::class
        );

        $this->app->bind(
            GeneralExamNextBackUseCaseInterface::class,
            GeneralExamNextBackUseCase::class
        );

        $this->app->bind(
            PullQuestionReportTasksUseCaseInterface::class,
            PullQuestionReportTasksUseCase::class
        );

        $this->app->bind(
            ReportQuestionReportUseCaseInterface::class,
            ReportQuestionReportUseCase::class
        );

        $this->app->bind(
            GetQuestionReportTasksUseCaseInterface::class,
            GetQuestionReportTasksUseCase::class
        );

        $this->app->bind(
            GeneralExamUseCaseInterface::class,
            GeneralExamUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\QuestionReport\UseCases\FillResource\FillResourceUseCaseInterface::class,
            \App\OurEdu\QuestionReport\UseCases\FillResource\FillResourceUseCase::class
        );
        $this->app->bind(
            \App\OurEdu\QuestionReport\UseCases\FillResource\Questions\TrueFalseUseCase\FillTrueFalseUseCaseInterface::class,
            \App\OurEdu\QuestionReport\UseCases\FillResource\Questions\TrueFalseUseCase\FillTrueFalseUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MultipleChoiceUseCase\FillMultipleChoiceUseCaseInterface::class,
            \App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MultipleChoiceUseCase\FillMultipleChoiceUseCase::class
        );
        $this->app->bind(
            \App\OurEdu\QuestionReport\UseCases\FillResource\Questions\DragDropUseCase\FillDragDropUseCaseUseCaseInterface::class,
            \App\OurEdu\QuestionReport\UseCases\FillResource\Questions\DragDropUseCase\FillDragDropUseCaseUseCase::class
        );
        $this->app->bind(
            \App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MatchingUseCase\FillMatchingUseCaseInterface::class,
            \App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MatchingUseCase\FillMatchingUseCase::class
        );
        $this->app->bind(
            \App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MultiMatchingUseCase\FillMultiMatchingUseCaseInterface::class,
            \App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MultiMatchingUseCase\FillMultiMatchingUseCase::class
        );

        $this->app->bind(
            StudentSubscribeUseCaseInterface::class,
            StudentSubscribeUseCase::class
        );

        $this->app->bind(
            MarkQuestionReportTaskAsDoneUseCaseInterface::class,
            MarkQuestionReportTaskAsDoneUseCase::class
        );

        $this->app->bind(
            FillHotSpotUseCaseInterface::class,
            FillHotSpotUseCase::class
        );
        $this->app->bind(
            CourseSubscribeUseCaseInterface::class,
            CourseSubscribeUseCase::class
        );

        $this->app->bind(
            RegisterStudentTeacherUseCaseInterface::class,
            RegisterStudentTeacherUseCase::class
        );

        $this->app->bind(
            CreateStudentTeacherUseCaseInterface::class,
            CreateStudentTeacherUseCase::class
        );

        $this->app->bind(
            NotifyParentsAboutSubjectProgressUseCaseInterface::class,
            NotifyParentsAboutSubjectProgressUseCase::class
        );

        $this->app->bind(
            TwitterStatelessUseCaseInterface::class,
            TwitterStatelessUseCase::class
        );

        $this->app->bind(
            EditResourceSubjectFormatSubjectUseCaseInterface::class,
            EditResourceSubjectFormatSubjectUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\Subjects\UseCases\EditResource\CompleteUseCase\FillCompleteUseCaseInterface::class,
            \App\OurEdu\Subjects\UseCases\EditResource\CompleteUseCase\FillCompleteUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\Subjects\UseCases\EditResource\DragDropUseCase\FillDragDropUseCaseUseCaseInterface::class,
            \App\OurEdu\Subjects\UseCases\EditResource\DragDropUseCase\FillDragDropUseCaseUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\Subjects\UseCases\EditResource\HotSpotUseCase\FillHotSpotUseCaseInterface::class,
            \App\OurEdu\Subjects\UseCases\EditResource\HotSpotUseCase\FillHotSpotUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\Subjects\UseCases\EditResource\MatchingUseCase\FillMatchingUseCaseInterface::class,
            \App\OurEdu\Subjects\UseCases\EditResource\MatchingUseCase\FillMatchingUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\Subjects\UseCases\EditResource\MultiMatchingUseCase\FillMultiMatchingUseCaseInterface::class,
            \App\OurEdu\Subjects\UseCases\EditResource\MultiMatchingUseCase\FillMultiMatchingUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\Subjects\UseCases\EditResource\MultipleChoiceUseCase\FillMultipleChoiceUseCaseInterface::class,
            \App\OurEdu\Subjects\UseCases\EditResource\MultipleChoiceUseCase\FillMultipleChoiceUseCase::class
        );


        $this->app->bind(
            \App\OurEdu\Subjects\UseCases\EditResource\TrueFalseUseCase\FillTrueFalseUseCaseInterface::class,
            \App\OurEdu\Subjects\UseCases\EditResource\TrueFalseUseCase\FillTrueFalseUseCase::class
        );

        $this->app->bind(
            RequestLiveSessionUseCaseInterface::class,
            RequestLiveSessionUseCase::class
        );

        $this->app->bind(
            VCRRequestUseCaseInterface::class,
            VCRRequestUseCase::class
        );

        $this->app->bind(
            VCRSessionUseCaseInterface::class,
            VCRSessionUseCase::class
        );

        $this->app->bind(
            StartGeneralExamUseCaseInterface::class,
            StartGeneralExamUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\GeneralExams\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCaseInterface::class,
            \App\OurEdu\GeneralExams\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCase::class
        );

        $this->app->bind(
            SMEListReportsUseCaseInterface::class,
            SMEListReportsUseCase::class
        );

        $this->app->bind(
            CourseRateUseCaseInterface::class,
            CourseRateUseCase::class
        );

        $this->app->bind(
            FinishGeneralExamUseCaseInterface::class,
            FinishGeneralExamUseCase::class
        );


        $this->app->bind(
            ReportGeneralExamReportUseCaseInterface::class,
            ReportGeneralExamReportUseCase::class
        );
        $this->app->bind(
            StudentOrderUseCaseInterface::class,
            StudentOrderUseCase::class
        );

        $this->app->bind(
            StudentSuccessRateUseCaseInterface::class,
            StudentSuccessRateUseCase::class
        );

        $this->app->bind(
            CreateStudentUseCaseInterface::class,
            CreateStudentUseCase::class
        );

        $this->app->bind(
            UpdateStudentUseCaseInterface::class,
            UpdateStudentUseCase::class
        );

        $this->app->bind(
            ReleaseTaskUseCaseInterface::class,
            ReleaseTaskUseCase::class
        );

        $this->app->bind(
            ExamChallengeUseCaseInterface::class,
            ExamChallengeUseCase::class
        );

        $this->app->bind(
            ExamTakeLikeUseCaseInterface::class,
            ExamTakeLikeUseCase::class
        );

        $this->app->bind(
            VCRNotificationUseCaseInterface::class,
            VCRNotificationUseCase::class
        );

        $this->app->bind(
            SchoolAccountUseCaseInterface::class,
            SchoolAccountUseCase::class
        );

        $this->app->bind(
            SchoolAccountBranchUseCaseInterface::class,
            SchoolAccountBranchUseCase::class
        );

        $this->app->bind(
            GetVCRSessionUseCaseInterface::class,
            GetVCRSessionUseCase::class
        );

        $this->app->bind(
            ResetSchoolInstructorPasswordUseCaseInterface::class,
            ResetSchoolInstructorPasswordUseCase::class
        );

        $this->app->bind(
            QuizUseCaseInterface::class,
            QuizUseCase::class
        );

        $this->app->bind(
            SessionPreparationUseCaseInterface::class,
            SessionPreparationUseCase::class
        );

        $this->app->bind(
            SchoolUsersUseCaseInterface::class,
            SchoolUsersUseCase::class
        );

        $this->app->bind(
            QuizQuestionUseCaseInterface::class,
            QuizQuestionUseCase::class
        );

        $this->app->bind(
            ImportJobRepositoryInterface::class,
            ImportJobRepository::class
        );

        $this->app->bind(
            ImportJobsUseCaseInterface::class,
            ImportJobsUseCase::class
        );

        $this->app->bind(
            StartQuizUseCaseInterface::class,
            StartQuizUseCase::class
        );

        $this->app->bind(
            AnswerQuizQuestionUseCaseInterface::class,
            AnswerQuizQuestionUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\Quizzes\UseCases\NextBackUseCase\NextBackUseCaseInterface::class,
            \App\OurEdu\Quizzes\UseCases\NextBackUseCase\NextBackUseCase::class
        );

        $this->app->bind(
            FinishQuizUseCaseInterface::class,
            FinishQuizUseCase::class
        );

        $this->app->bind(
            HomeWorkUseCaseInterface::class,
            HomeWorkUseCase::class
        );

        $this->app->bind(
            GeneralQuizHomeworkUseCaseInterface::class,
            GeneralQuizHomeworkUseCase::class
        );

        $this->app->bind(
            CreateHomeworkUseCaseInterface::class,
            CreateHomeworkUseCase::class
        );

        $this->app->bind(
            GeneralQuizQuestionUseCaseInterface::class,
            GeneralQuizQuestionUseCase::class
        );

        $this->app->bind(
            GeneralQuizMultipleChoiceUseCaseInterface::class,
            GeneralQuizMultipleChoiceUseCase::class
        );

        $this->app->bind(
            UpdateHomeworkUseCaseInterface::class,
            UpdateHomeworkUseCase::class
        );

        $this->app->bind(
            GeneralTrueFalseUseCaseInterface::class,
            GeneralTrueFalseUseCase::class
        );

        $this->app->bind(
            TrueFalseQuestionWithCorrectUseCaseInterface::class,
            TrueFalseQuestionWithCorrectUseCase::class
        );

        $this->app->bind(
            GeneralEssayCaseInterface::class,
            GeneralEssayUseCase::class
        );

        $this->app->bind(
            StartGeneralQuizUseCaseInterface::class,
            StartGeneralQuizUseCase::class
        );

        $this->app->bind(
            EssayPostAnswerUseCaseInterface::class,
            EssayPostAnswerUseCase::class
        );

        $this->app->bind(
            GeneralQuizDragDropUseCaseInterface::class,
            GeneralQuizDragDropUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\DragDropPostAnswerUseCase\DragDropPostAnswerUseCaseInterface::class,
            \App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\DragDropPostAnswerUseCase\DragDropPostAnswerUseCase::class
        );
        $this->app->bind(
            AddQuestionBankToGeneralQuizInterface::class,
            AddQuestionBankToGeneralQuiz::class
        );

        $this->app->bind(
            CompleteQuestionUseCaseInterface::class,
            CompleteQuestionUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\CompletePostAnswerUseCase\CompletePostAnswerUseCaseInterface::class,
            \App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\CompletePostAnswerUseCase\CompletePostAnswerUseCase::class
        );

        $this->app->bind(
            RetakeGeneralQuizUseCaseInterface::class,
            RetakeGeneralQuizUseCase::class
        );


        $this->app->bind(
            CreatePeriodicTestUseCaseInterface::class,
            CreatePeriodicTestUseCase::class
        );

        $this->app->bind(
            UpdatePeriodicTestUseCaseInterface::class,
            UpdatePeriodicTestUseCase::class
        );

        $this->app->bind(
            \App\OurEdu\GeneralQuizzes\PeriodicTest\UseCases\RetakeGeneralQuizUseCaseInterface::class,
            \App\OurEdu\GeneralQuizzes\PeriodicTest\UseCases\RetakeGeneralQuizUseCase::class
        );

        $this->app->bind(
            ViewAsStudentUseCaseInterface::class,
            ViewAsStudentUseCase::class
        );

        $this->app->bind(
            ViewAsAssessorUseCaseInterface::class,
            ViewAsAssessorUseCase::class
        );

        $this->app->bind(
            ThankingCertificatesUseCaseInterface::class,
            ThankingCertificatesUseCase::class
        );

        $this->app->bind(
            GenerateTokenInterface::class,
            GenerateTokenUseCase::class
        );

        $this->app->bind(
            VideoCallUseCaseInterface::class,
            VideoCallUseCase::class
        );

        $this->app->bind(
            StudentPeriodicTestTimeUseCaseInterface::class,
            StudentPeriodicTestTimeUseCase::class
        );

        $this->app->bind(
            CreateAssessmentUseCaseInterface::class,
            CreateAssessmentUseCase::class
        );


        $this->app->bind(
            UpdateAssessmentUseCaseInterface::class,
            UpdateAssessmentUseCase::class
        );

        $this->app->bind(
            AssessmentQuestionUseCaseInterface::class,
            AssessmentQuestionUseCase::class
        );

        $this->app->bind(
            AssessmentMultipleChoiceUseCaseInterface::class,
            AssessmentMultipleChoiceUseCase::class
        );

        $this->app->bind(
            StarRatingUseCaseInterface::class,
            StarRatingUseCase::class
        );


        $this->app->bind(
            ScaleRatingUseCaseInterface::class,
            ScaleRatingUseCase::class
        );

        $this->app->bind(
            MatrixUseCaseInterface::class,
            MatrixUseCase::class
        );


        $this->app->bind(
            StartAssessmentUseCaseInterface::class,
            StartAssessmentUseCase::class
        );

        $this->app->bind(
            SatisficationRatingUseCaseInterface::class,
            SatisficationRatingUseCase::class
        );

        $this->app->bind(
            AssessmentPostAnswerUseCaseInterface::class,
            AssessmentPostAnswerUseCase::class
        );

        $this->app->bind(
            AssessmentMultipleChoicePostAnswerUseCaseInterface::class,
            AssessmentMultipleChoicePostAnswerUseCase::class
        );

        $this->app->bind(
            ScaleRatingPostAnswerInterface::class,
            ScaleRatingPostAnswer::class
        );

        $this->app->bind(
            StarRatingPostAnswerUseCaseInterface::class,
            StarRatingPostAnswerUseCase::class
        );

        $this->app->bind(
            SatisfactionPostAnswerUseCaseInterface::class,
            SatisfactionPostAnswerUseCase::class
        );

        $this->app->bind(
            MatrixPostAnswerUseCaseInterface::class,
            MatrixPostAnswerUseCase::class
        );

        $this->app->bind(
            FinishAssessmentUseCaseInterface::class,
            FinishAssessmentUseCase::class
        );

        $this->app->bind(
            AssessmentPointRateUseCaseInterface::class,
            AssessmentPointRateUseCase::class
        );

        $this->app->bind(
            AssessmentNextBackUseCaseInterface::class,
            AssessmentNextBackUseCase::class
        );

        $this->app->bind(
            CloneAssessmentUseCaseInterface::class,
            CloneAssessmentUseCase::class
        );

        $this->app->bind(
            CreateZoomUserUseCaseInterface::class,
            CreateZoomUserUseCase::class
        );

        $this->app->bind(
            EssayUseCaseInterface::class,
            EssayUseCase::class
        );

        $this->app->bind(
            EssayQuestionPostAnswerUseCaseInterface::class,
            EssayQuestionPostAnswerUseCase::class
        );
        $this->app->bind(
            CreateSchoolAdminUseCaseInterface::class,
            CreateSchoolAdminUseCase::class,
        );
        $this->app->bind(
            SchoolAdminRepositoryInterface::class,
            SchoolAdminRepository::class,
        );

        $this->app->bind(
            UpdateSchoolAdminUseCaseInterface::class,
            UpdateSchoolAdminUseCase::class
        );

        $this->app->bind(
            GeneralUseCaseInterface::class,
            GeneralUseCase::class
        );

        $this->app->bind(
            SendLoginOtp::class,
            SendLoginOtpImp::class
        );

        $this->app->bind(
            CreateFormativeTestUseCaseInterface::class,
            CreateFormativeTestUseCase::class
        );

        $this->app->bind(
            UpdateFormativeTestUseCaseInterface::class,
            UpdateFormativeTestUseCase::class
        );

        $this->app->bind(
            CourseMediaUseCaseInterface::class,
            CourseMediaUseCase::class
        );

        $this->app->bind(
            CreateCourseHomeworkUseCaseInterface::class,
            CreateCourseHomeworkUsecase::class
        );

        $this->app->bind(
            UpdateCourseHomeworkUseCaseInterface::class,
            UpdateCourseHomeworkUseCase::class
        );

        $this->app->bind(
            CloneFormativeUseCaseInterface::class,
            CloneFormativeUseCase::class
        );

        $this->app->bind(
            CloneQuestionsUseCaseInterface::class,
            CloneQuestionsUseCase::class
        );

        $this->app->bind(
            SendActivationSmsUseCaseInterface::class,
            SendActivationSmsUseCase::class
        );
        $this->app->bind(
            \App\OurEdu\Subjects\UseCases\SubscribeUseCase\SubscribeUseCaseInterface::class,
            \App\OurEdu\Subjects\UseCases\SubscribeUseCase\SubscribeUseCase::class,
        );
        $this->app->bind(
            \App\OurEdu\Courses\UseCases\CourseSubscribeUseCase\CourseSubscribeUseCaseInterface::class,
            \App\OurEdu\Courses\UseCases\CourseSubscribeUseCase\CourseSubscribeUseCase::class,
        );
    }
}
