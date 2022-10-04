<?php

namespace App\OurEdu\BaseApp\Providers;

use App\OurEdu\AppVersions\Repository\AppVersionRepository;
use App\OurEdu\AppVersions\Repository\AppVersionRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\EassyQuestions\AssessmentEssayQuestionRepository;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\EassyQuestions\AssessmentEssayQuestionRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentCategoryRepository;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentCategoryRepositoryInterface;
use App\OurEdu\Certificates\Repositories\ThankingCertificatesRepository;
use App\OurEdu\Certificates\Repositories\ThankingCertificatesRepositoryInterface;
use App\OurEdu\Contact\Repository\ContactInterface;
use App\OurEdu\Contact\Repository\ContactRepository;
use App\OurEdu\Courses\Repository\CourseMediaRepository;
use App\OurEdu\Courses\Repository\CourseMediaRepositoryInterface;
use App\OurEdu\Courses\UseCases\CourseMediaUseCase\CourseMediaUseCase;
use App\OurEdu\Courses\UseCases\CourseMediaUseCase\CourseMediaUseCaseInterface;
use App\OurEdu\Events\Repositories\StudentStoredEventRepository;
use App\OurEdu\Events\Repositories\StudentStoredEventRepositoryInterface;
use App\OurEdu\GeneralExamReport\Repository\GeneralExamReportRepository;
use App\OurEdu\GeneralExamReport\Repository\GeneralExamReportRepositoryInterface;
use App\OurEdu\GeneralExamReport\Repository\GeneralExamReportTaskRepository;
use App\OurEdu\GeneralExamReport\Repository\GeneralExamReportTaskRepositoryInterface;
use App\OurEdu\GeneralExams\Repository\GeneralExamStudent\GeneralExamStudentRepository;
use App\OurEdu\GeneralExams\Repository\GeneralExamStudent\GeneralExamStudentRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Homework\UseCases\HomeworkRepository;
use App\OurEdu\GeneralQuizzes\Homework\UseCases\HomeworkRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentAnswerRepository;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentAnswerRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepository;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepositoryInterface;
use App\OurEdu\Payments\Repository\TransactionRepository;
use App\OurEdu\Payments\Repository\TransactionRepositoryInterface;
use App\OurEdu\QuestionReport\Repository\QuestionReportSubjectFormatSubjectRepository;
use App\OurEdu\QuestionReport\Repository\QuestionReportSubjectFormatSubjectRepositoryInterface;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepository;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use App\OurEdu\Reports\Repository\ReportSubjectFormatSubjectRepository;
use App\OurEdu\Reports\Repository\ReportSubjectFormatSubjectRepositoryInterface;

use App\OurEdu\ResourceSubjectFormats\Repository\Essay\EssayRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Essay\EssayRepositoryInterface;
use App\OurEdu\Roles\Repository\RoleRepository;
use App\OurEdu\Roles\Repository\RoleRepositoryInterface;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepository;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClass\Repositories\ClassroomClassRepository;
use App\OurEdu\SchoolAccounts\ClassroomClass\Repositories\ClassroomClassRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Repositories\ClassroomClassSessionRepository;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Repositories\ClassroomClassSessionRepositoryInterface;
use App\OurEdu\SchoolAccounts\SessionPreparations\Repositories\SessionPreparationRepository\SessionPreparationRepository;
use App\OurEdu\SchoolAccounts\SessionPreparations\Repositories\SessionPreparationRepository\SessionPreparationRepositoryInterface;

use App\OurEdu\StaticPages\Repository\DistinguishedStudentsRepository;
use App\OurEdu\StaticPages\Repository\DistinguishedStudentsRepositoryInterface;
use App\OurEdu\TextChat\Repository\TextChatRepository;
use App\OurEdu\TextChat\Repository\TextChatRepositoryInterface;
use App\OurEdu\TextChat\ServiceManager\ChatServiceManager;
use App\OurEdu\TextChat\ServiceManager\ChatServiceManagerInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepository;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionPresenceRepository;
use App\OurEdu\VCRSchedules\Repository\VCRSessionPresenceRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepository;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
use App\OurEdu\VCRSessions\Repositories\ZoomHostRepository;
use App\OurEdu\VCRSessions\Repositories\ZoomHostRepositoryInterface;
use App\OurEdu\VCRSessions\ServiceManager\AgoraServiceManager;
use App\OurEdu\VCRSessions\ServiceManager\AgoraServiceManagerInterface;
use App\OurEdu\VideoCall\Repositories\VideoCallRepository;
use App\OurEdu\VideoCall\Repositories\VideoCallRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\OurEdu\Config\Repository\ConfigRepository;
use App\OurEdu\Subjects\Repository\TaskRepository;
use App\OurEdu\Users\Repository\StudentRepository;
use App\OurEdu\Courses\Repository\CourseRepository;
use App\OurEdu\Options\Repository\OptionRepository;
use App\OurEdu\Reports\Repository\ReportRepository;
use App\OurEdu\Schools\Repository\SchoolRepository;
use App\OurEdu\Users\Repository\UserLogsRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Users\Repository\InstructorRepository;
use App\OurEdu\Countries\Repository\CountryRepository;
use App\OurEdu\Feedbacks\Repository\FeedbackRepository;
use App\OurEdu\Courses\Repository\LiveSessionRepository;
use App\OurEdu\Users\Repository\ContentAuthorRepository;
use App\OurEdu\Users\Repository\FirebaseTokenRepository;
use App\OurEdu\Subjects\Repository\SubjectLogsRepository;
use App\OurEdu\Users\Repository\StudentTeacherRepository;
use App\OurEdu\Courses\Repository\CourseSessionRepository;
use App\OurEdu\Config\Repository\ConfigRepositoryInterface;
use App\OurEdu\Invitations\Repository\InvitationRepository;
use App\OurEdu\Subjects\Repository\TaskRepositoryInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use App\OurEdu\Courses\Repository\CourseRepositoryInterface;
use App\OurEdu\GradeClasses\Repository\GradeClassRepository;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use App\OurEdu\Reports\Repository\ReportRepositoryInterface;
use App\OurEdu\Schools\Repository\SchoolRepositoryInterface;
use App\OurEdu\StaticPages\Repository\StaticPagesRepository;
use App\OurEdu\Subscribes\Repository\SubscriptionRepository;
use App\OurEdu\Users\Repository\UserLogsRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRRequestRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRScheduleRepository;
use App\OurEdu\StaticBlocks\Repository\StaticBlocksRepository;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Users\Repository\InstructorRepositoryInterface;
use App\OurEdu\AcademicYears\Repository\AcademicYearRepository;
use App\OurEdu\Countries\Repository\CountryRepositoryInterface;
use App\OurEdu\Notifications\Repository\NotificationRepository;
use App\OurEdu\Feedbacks\Repository\FeedbackRepositoryInterface;
use App\OurEdu\Payments\Repository\PaymentTransactionRepository;
use App\OurEdu\Courses\Repository\LiveSessionRepositoryInterface;
use App\OurEdu\Users\Repository\ContentAuthorRepositoryInterface;
use App\OurEdu\Users\Repository\FirebaseTokenRepositoryInterface;
use App\OurEdu\QuestionReport\Repository\QuestionReportRepository;
use App\OurEdu\Subjects\Repository\SubjectFormatSubjectRepository;
use App\OurEdu\Subjects\Repository\SubjectLogsRepositoryInterface;
use App\OurEdu\Users\Repository\StudentTeacherRepositoryInterface;
use App\OurEdu\Courses\Repository\CourseSessionRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Pdf\PdfRepository;
use App\OurEdu\SubjectPackages\Repository\SubjectPackageRepository;
use App\OurEdu\Exams\Repository\ExamQuestion\ExamQuestionRepository;
use App\OurEdu\Invitations\Repository\InvitationRepositoryInterface;
use App\OurEdu\GradeClasses\Repository\GradeClassRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Page\PageRepository;
use App\OurEdu\StaticPages\Repository\StaticPagesRepositoryInterface;
use App\OurEdu\Subscribes\Repository\SubscriptionRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRRequestRepositoryInterface;
use App\OurEdu\QuestionReport\Repository\QuestionReportTaskRepository;
use App\OurEdu\VCRSchedules\Repository\VCRScheduleRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Audio\AudioRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Flash\FlashRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Video\VideoRepository;
use App\OurEdu\StaticBlocks\Repository\StaticBlocksRepositoryInterface;
use App\OurEdu\AcademicYears\Repository\AcademicYearRepositoryInterface;
use App\OurEdu\Notifications\Repository\NotificationRepositoryInterface;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepository;
use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepository;
use App\OurEdu\Payments\Repository\PaymentTransactionRepositoryInterface;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalTestRepository;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalOptionRepository;
use App\OurEdu\QuestionReport\Repository\QuestionReportRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\HotSpot\HotSpotRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Picture\PictureRepository;
use App\OurEdu\Subjects\Repository\SubjectFormatSubjectRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Pdf\PdfRepositoryInterface;
use App\OurEdu\SubjectPackages\Repository\SubjectPackageRepositoryInterface;
use App\OurEdu\Exams\Repository\ExamQuestion\ExamQuestionRepositoryInterface;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalQuestionRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Complete\CompleteRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\DragDrop\DragDropRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Matching\MatchingRepository;
use App\OurEdu\GeneralExams\Repository\Question\GeneralExamQuestionRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Page\PageRepositoryInterface;
use App\OurEdu\QuestionReport\Repository\QuestionReportTaskRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse\TrueFalseRepository;
use App\OurEdu\Exams\Repository\ExamQuestionAnswer\ExamQuestionAnswerRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Audio\AudioRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Flash\FlashRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Video\VideoRepositoryInterface;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use App\OurEdu\Exams\Repository\PrepareExamQuestion\PrepareExamQuestionRepository;
use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepositoryInterface;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalRecomendationRepository;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalTestRepositoryInterface;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalOptionRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\HotSpot\HotSpotRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Picture\PictureRepositoryInterface;
use App\OurEdu\Subjects\Repository\StudentProgress\ResourceProgressStudentRepository;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalQuestionRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Complete\CompleteRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\DragDrop\DragDropRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Matching\MatchingRepositoryInterface;
use App\OurEdu\GeneralExams\Repository\Question\GeneralExamQuestionRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\MultiMatching\MultiMatchingRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\ResourceSubjectFormatSubjectRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse\TrueFalseRepositoryInterface;
use App\OurEdu\Exams\Repository\ExamQuestionAnswer\ExamQuestionAnswerRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\MultipleChoice\MultipleChoiceRepository;
use App\OurEdu\Subjects\Repository\StudentProgress\SubjectFormatProgressStudentRepository;
use App\OurEdu\Exams\Repository\PrepareExamQuestion\PrepareExamQuestionRepositoryInterface;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalRecomendationRepositoryInterface;
use App\OurEdu\GeneralExams\Repository\PreparedQuestion\PreparedGeneralExamQuestionRepository;
use App\OurEdu\Subjects\Repository\StudentProgress\ResourceProgressStudentRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\MultiMatching\MultiMatchingRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\ResourceSubjectFormatSubjectRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\MultipleChoice\MultipleChoiceRepositoryInterface;
use App\OurEdu\Subjects\Repository\StudentProgress\SubjectFormatProgressStudentRepositoryInterface;
use App\OurEdu\GeneralExams\Repository\PreparedQuestion\PreparedGeneralExamQuestionRepositoryInterface;
use App\OurEdu\VCRSessions\ServiceManager\OpenTokServiceManagerInterface;
use App\OurEdu\VCRSessions\ServiceManager\OpenTokServiceManager;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepository;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepository;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\MultipleChoice\MultipleChoiceRepositoryInterface as AssessmentMultipleChoiceRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\MultipleChoice\MultipleChoiceRepository as AssessmentMultipleChoiceRepository;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\AssessmentQuestionRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\AssessmentQuestionRepository;

use App\OurEdu\Assessments\Repositories\AssessmentQuestions\RatingQuestion\RatingQuestionRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\RatingQuestion\RatingQuestionRepository;

use App\OurEdu\Assessments\Repositories\AssessmentQuestions\MatrixQuestion\MatrixQuestionRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\MatrixQuestion\MatrixQuestionRepository;

use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentAssessorRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentAssessorRepository;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentPointsRateRepository;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentPointsRateRepositoryInterface;

use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepository;
use App\OurEdu\Courses\Repository\DiscussionCommentRepository;
use App\OurEdu\Courses\Repository\DiscussionCommentRepositoryInterface;
use App\OurEdu\LandingPage\Repositories\LandingPageRepository;
use App\OurEdu\LandingPage\Repositories\LandingPageRepositoryInterface;

class RepositoriesServiceProviders extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            'App\OurEdu\Users\Repository\UserRepositoryInterface',
            'App\OurEdu\Users\Repository\UserRepository'
        );

        $this->app->bind(
            GeneralQuizStudentRepositoryInterface::class,
            GeneralQuizStudentRepository::class
        );

        $this->app->bind(
            CountryRepositoryInterface::class,
            CountryRepository::class
        );

        $this->app->bind(
            AcademicYearRepositoryInterface::class,
            AcademicYearRepository::class
        );

        $this->app->bind(
            EducationalSystemRepositoryInterface::class,
            EducationalSystemRepository::class
        );

        $this->app->bind(
            GradeClassRepositoryInterface::class,
            GradeClassRepository::class
        );

        $this->app->bind(
            SubjectRepositoryInterface::class,
            SubjectRepository::class
        );

        $this->app->bind(
            ContentAuthorRepositoryInterface::class,
            ContentAuthorRepository::class
        );

        $this->app->bind(
            SchoolRepositoryInterface::class,
            SchoolRepository::class
        );

        $this->app->bind(
            InstructorRepositoryInterface::class,
            InstructorRepository::class
        );

        $this->app->bind(
            OptionRepositoryInterface::class,
            OptionRepository::class
        );

        $this->app->bind(
            TaskRepositoryInterface::class,
            TaskRepository::class
        );

        $this->app->bind(
            ResourceSubjectFormatSubjectRepositoryInterface::class,
            ResourceSubjectFormatSubjectRepository::class
        );

        $this->app->bind(
            TrueFalseRepositoryInterface::class,
            TrueFalseRepository::class
        );

        $this->app->bind(
            VideoRepositoryInterface::class,
            VideoRepository::class
        );

        $this->app->bind(
            MatchingRepositoryInterface::class,
            MatchingRepository::class
        );

        $this->app->bind(
            MultiMatchingRepositoryInterface::class,
            MultiMatchingRepository::class
        );

        $this->app->bind(
            DragDropRepositoryInterface::class,
            DragDropRepository::class
        );

        $this->app->bind(
            PageRepositoryInterface::class,
            PageRepository::class
        );

        $this->app->bind(
            AudioRepositoryInterface::class,
            AudioRepository::class
        );

        $this->app->bind(
            FlashRepositoryInterface::class,
            FlashRepository::class
        );

        $this->app->bind(
            PdfRepositoryInterface::class,
            PdfRepository::class
        );

        $this->app->bind(
            MultipleChoiceRepositoryInterface::class,
            MultipleChoiceRepository::class
        );

        $this->app->bind(
            PictureRepositoryInterface::class,
            PictureRepository::class
        );
        $this->app->bind(
            PrepareExamQuestionRepositoryInterface::class,
            PrepareExamQuestionRepository::class
        );

        $this->app->bind(
            StudentRepositoryInterface::class,
            StudentRepository::class
        );

        $this->app->bind(
            FeedbackRepositoryInterface::class,
            FeedbackRepository::class
        );

        $this->app->bind(
            PsychologicalOptionRepositoryInterface::class,
            PsychologicalOptionRepository::class
        );

        $this->app->bind(
            ReportRepositoryInterface::class,
            ReportRepository::class
        );

        $this->app->bind(
            SubscriptionRepositoryInterface::class,
            SubscriptionRepository::class
        );

        $this->app->bind(
            ExamRepositoryInterface::class,
            ExamRepository::class
        );
        $this->app->bind(
            ExamQuestionRepositoryInterface::class,
            ExamQuestionRepository::class
        );

        $this->app->bind(
            ExamQuestionAnswerRepositoryInterface::class,
            ExamQuestionAnswerRepository::class
        );

        $this->app->bind(
            InvitationRepositoryInterface::class,
            InvitationRepository::class
        );

        $this->app->bind(
            SubjectLogsRepositoryInterface::class,
            SubjectLogsRepository::class
        );

        $this->app->bind(
            ResourceProgressStudentRepositoryInterface::class,
            ResourceProgressStudentRepository::class
        );

        $this->app->bind(
            SubjectFormatProgressStudentRepositoryInterface::class,
            SubjectFormatProgressStudentRepository::class
        );

        $this->app->bind(
            QuestionReportRepositoryInterface::class,
            QuestionReportRepository::class
        );

        $this->app->bind(
            PaymentTransactionRepositoryInterface::class,
            PaymentTransactionRepository::class
        );

        $this->app->bind(
            PsychologicalTestRepositoryInterface::class,
            PsychologicalTestRepository::class
        );

        $this->app->bind(
            GeneralExamQuestionRepositoryInterface::class,
            GeneralExamQuestionRepository::class
        );

        $this->app->bind(
            PsychologicalQuestionRepositoryInterface::class,
            PsychologicalQuestionRepository::class
        );

        $this->app->bind(
            CompleteRepositoryInterface::class,
            CompleteRepository::class
        );

        $this->app->bind(
            QuestionReportTaskRepositoryInterface::class,
            QuestionReportTaskRepository::class
        );

        $this->app->bind(
            PsychologicalRecomendationRepositoryInterface::class,
            PsychologicalRecomendationRepository::class
        );

        $this->app->bind(
            CourseRepositoryInterface::class,
            CourseRepository::class
        );

        $this->app->bind(
            FirebaseTokenRepositoryInterface::class,
            FirebaseTokenRepository::class
        );

        $this->app->bind(
            VCRScheduleRepositoryInterface::class,
            VCRScheduleRepository::class
        );

        $this->app->bind(
            ConfigRepositoryInterface::class,
            ConfigRepository::class
        );
        $this->app->bind(
            AppVersionRepositoryInterface::class,
            AppVersionRepository::class
        );

        $this->app->bind(
            PreparedGeneralExamQuestionRepositoryInterface::class,
            PreparedGeneralExamQuestionRepository::class
        );

        $this->app->bind(
            GeneralExamRepositoryInterface::class,
            GeneralExamRepository::class
        );

        $this->app->bind(
            CourseSessionRepositoryInterface::class,
            CourseSessionRepository::class
        );

        $this->app->bind(
            StaticPagesRepositoryInterface::class,
            StaticPagesRepository::class
        );

        $this->app->bind(
            StaticBlocksRepositoryInterface::class,
            StaticBlocksRepository::class
        );

        $this->app->bind(
            LiveSessionRepositoryInterface::class,
            LiveSessionRepository::class
        );

        $this->app->bind(
            UserLogsRepositoryInterface::class,
            UserLogsRepository::class
        );

        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class
        );

        $this->app->bind(
            SubjectPackageRepositoryInterface::class,
            SubjectPackageRepository::class
        );

        $this->app->bind(
            HotSpotRepositoryInterface::class,
            HotSpotRepository::class
        );

        $this->app->bind(
            SubjectFormatSubjectRepositoryInterface::class,
            SubjectFormatSubjectRepository::class
        );

        $this->app->bind(
            StudentTeacherRepositoryInterface::class,
            StudentTeacherRepository::class
        );

        $this->app->bind(
            VCRRequestRepositoryInterface::class,
            VCRRequestRepository::class
        );

        $this->app->bind(
            VCRSessionRepositoryInterface::class,
            VCRSessionRepository::class
        );
        $this->app->bind(
            GeneralExamStudentRepositoryInterface::class,
            GeneralExamStudentRepository::class
        );
        $this->app->bind(
            GeneralExamReportRepositoryInterface::class,
            GeneralExamReportRepository::class
        );

        $this->app->bind(
            ContactInterface::class,
            ContactRepository::class
        );

        $this->app->bind(
            DistinguishedStudentsRepositoryInterface::class,
            DistinguishedStudentsRepository::class
        );

        $this->app->bind(
            ReportSubjectFormatSubjectRepositoryInterface::class,
            ReportSubjectFormatSubjectRepository::class
        );

        $this->app->bind(
            QuestionReportSubjectFormatSubjectRepositoryInterface::class,
            QuestionReportSubjectFormatSubjectRepository::class
        );

        $this->app->bind(
            OpenTokServiceManagerInterface::class,
            OpenTokServiceManager::class
        );
        $this->app->bind(
            GeneralExamReportTaskRepositoryInterface::class,
            GeneralExamReportTaskRepository::class
        );

        $this->app->bind(
            ChatServiceManagerInterface::class,
            ChatServiceManager::class
        );

        $this->app->bind(
            StudentStoredEventRepositoryInterface::class,
            StudentStoredEventRepository::class
        );

        $this->app->bind(
            AgoraServiceManagerInterface::class,
            AgoraServiceManager::class
        );

        $this->app->bind(
            TransactionRepositoryInterface::class,
            TransactionRepository::class
        );

        $this->app->bind(
            VCRSessionParticipantsRepositoryInterface::class,
            VCRSessionParticipantsRepository::class
        );

        $this->app->bind(
            VCRSessionPresenceRepositoryInterface::class,
            VCRSessionPresenceRepository::class
        );

        $this->app->bind(
            ClassroomClassRepositoryInterface::class,
            ClassroomClassRepository::class
        );

        $this->app->bind(
            RoleRepositoryInterface::class,
            RoleRepository::class
        );

        $this->app->bind(
            QuizRepositoryInterface::class,
            QuizRepository::class
        );

        $this->app->bind(
            SessionPreparationRepositoryInterface::class,
            SessionPreparationRepository::class
        );

        $this->app->bind(
            ClassroomRepositoryInterface::class,
            ClassroomRepository::class
        );

        $this->app->bind(
            ClassroomClassSessionRepositoryInterface::class,
            ClassroomClassSessionRepository::class
        );

        $this->app->bind(
            GeneralQuizRepositoryInterface::class,
            GeneralQuizRepository::class
        );

        $this->app->bind(
            QuestionBankRepositoryInterface::class,
            QuestionBankRepository::class
        );

        $this->app->bind(
            EssayRepositoryInterface::class,
            EssayRepository::class
        );

        $this->app->bind(
            HomeworkRepositoryInterface::class,
            HomeworkRepository::class
        );

        $this->app->bind(
            GeneralQuizStudentAnswerRepositoryInterface::class,
            GeneralQuizStudentAnswerRepository::class
        );
        $this->app->bind(
            ThankingCertificatesRepositoryInterface::class,
            ThankingCertificatesRepository::class
        );

        $this->app->bind(
            VideoCallRepositoryInterface::class,
            VideoCallRepository::class
        );

        $this->app->bind(
            AssessmentRepositoryInterface::class,
            AssessmentRepository::class
        );

        $this->app->bind(
            AssessmentMultipleChoiceRepositoryInterface::class,
            AssessmentMultipleChoiceRepository::class
        );

        $this->app->bind(
            AssessmentQuestionRepositoryInterface::class,
            AssessmentQuestionRepository::class
        );


        $this->app->bind(
            RatingQuestionRepositoryInterface::class,
            RatingQuestionRepository::class
        );


        $this->app->bind(
            MatrixQuestionRepositoryInterface::class,
            MatrixQuestionRepository::class
        );

        $this->app->bind(
            AssessmentUsersRepositoryInterface::class,
            AssessmentUsersRepository::class
        );

        $this->app->bind(
            AssessmentPointsRateRepositoryInterface::class,
            AssessmentPointsRateRepository::class
        );

        $this->app->bind(
            AssessmentEssayQuestionRepositoryInterface::class,
            AssessmentEssayQuestionRepository::class
        );

        $this->app->bind(
            ZoomHostRepositoryInterface::class,
            ZoomHostRepository::class
        );

        $this->app->bind(
            LandingPageRepositoryInterface::class,
            LandingPageRepository::class
        );

        $this->app->bind(
            AssessmentCategoryRepositoryInterface::class,
            AssessmentCategoryRepository::class
        );

        $this->app->bind(
            DiscussionCommentRepositoryInterface::class,
            DiscussionCommentRepository::class
        );

        $this->app->bind(
            CourseMediaRepositoryInterface::class,
            CourseMediaRepository::class
        );
        $this->app->bind(
            CourseMediaUseCaseInterface::class,
            CourseMediaUseCase::class
        );
    }
}
