<?php


namespace App\OurEdu\Exams\UseCases\AnswerUseCase\HotSpotPostAnswerUseCase;

use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\Repository\ExamQuestionAnswer\ExamQuestionAnswerRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotMedia;
use App\OurEdu\Users\Models\Student;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;

class HotSpotPostAnswerUseCase implements HotSpotPostAnswerUseCaseInterface
{
    private $examRepository;
    private $examQuestionAnswerRepository;
    private $student;

    public function __construct(
        ExamRepositoryInterface $examRepository,
        ExamQuestionAnswerRepositoryInterface $examQuestionAnswerRepository
    ) {
        $this->examRepository = $examRepository;
        $this->examQuestionAnswerRepository = $examQuestionAnswerRepository;
    }

    /**
     * @param ExamRepository $examRepository
     * @param ExamQuestion $examQuestion
     * @param Collection $answers
     * @return mixed|void
     */
    public function postAnswer(ExamRepository $examRepository, ExamQuestion $examQuestion, Collection $answers)
    {
        $examType = $examRepository->getExamType();
        $this->student = Auth::guard('api')->user()->student;

        $mainQuestion = $examRepository->getQuestion($examQuestion);
        // delete previous answers for this student
        $this->examQuestionAnswerRepository->deleteQuestionAnswers($examQuestion, $this->student->id);
        $hotSpotMedia = HotSpotMedia::where('res_hot_spot_data_id', $mainQuestion->res_hot_spot_data_id)->first();

        foreach ($answers as $answer) {
            $mainQuestionAnswer = HotSpotAnswer::where('res_hot_spot_question_id', $mainQuestion->id)->first();
            $foundedAnswer = $this->checkCorrectAnswer(
                $answer,
                $hotSpotMedia->image_width,
                $hotSpotMedia->image_height,
                $mainQuestionAnswer
            );

            $coordinates = array(
                "x" => $answer->x_coordinate,
                "y" => $answer->y_coordinate,
            );

            $image = array(
                "width" => $answer->image_width,
                "height" => $answer->image_height,
            );

            $answerText = json_encode(array('coordinates' => $coordinates, 'image' => $image));
            $answerData = [
                'question_id' => $examQuestion->id,
                'answer_text' => $answerText,
                'is_correct_answer' => $foundedAnswer,
                'option_table_type' => HotSpotAnswer::class,
                'option_table_id' => $mainQuestionAnswer->id,
            ];

            $examRepository->insertAnswers($examQuestion->id, $answerData);

            $examRepository->updateExamQuestion(
                $examQuestion->id,
                ['is_correct_answer' => $foundedAnswer, 'is_answered' => 1]
            );

            switch ($examType) {
                case ExamTypes::PRACTICE:
                    $this->assignQuestionToStudentPractise($mainQuestion);
                    break;
                case ExamTypes::COURSE_COMPETITION:
                case ExamTypes::COMPETITION:
                    $examRepository->insertCompetitionQuestionResult($examQuestion->id, $foundedAnswer);
                    break;
                case ExamTypes::INSTRUCTOR_COMPETITION:
                    $examRepository->insertInstructorCompetitionQuestionResult($examQuestion->id, $foundedAnswer);
                    break;
            }
        }
    }

    public function checkCorrectAnswer($answer, $baseImageWidth, $baseImageHeight, $mainQuestionAnswer)
    {
        $this->student = Auth::guard('api')->user()->student;

        $xratio = $baseImageWidth / $answer->image_width;
        $yratio = $baseImageHeight / $answer->image_height;

        // accurate coordinates are the main question answer
        // to check if student answer correct, check if it is on polygon or not
        $polygon = json_decode($mainQuestionAnswer->answer, true);

        // todo make sure the last coordinate in polygon is same as the first, if not append it to polygon.
        foreach ($polygon as $coordinate) {
            $coordinate['x'] *= $xratio;
            $coordinate['y'] *= $yratio;
        }
        $point = array('x' => $answer->x_coordinate, 'y' => $answer->y_coordinate);


        return $this->pointInPolygon($point, $polygon);
    }


    public function pointInPolygon($point, $polygon, $pointOnVertex = true)
    {
        $this->student = Auth::guard('api')->user()->student;

        $this->pointOnVertex = $pointOnVertex;

        $vertices = $polygon;

        // Check if the point sits exactly on a vertex
        if ($this->pointOnVertex == true and $this->pointOnVertex($point, $vertices) == true) {
            return true;
        }

        // Check if the point is inside the polygon or on the boundary
        $intersections = 0;
        $vertices_count = count($vertices);

        for ($i = 1; $i < $vertices_count; $i++) {
            $vertex1 = $vertices[$i - 1];
            $vertex2 = $vertices[$i];
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min(
                    $vertex1['x'],
                    $vertex2['x']
                ) and $point['x'] < max(
                    $vertex1['x'],
                    $vertex2['x']
                )) { // Check if point is on an horizontal polygon boundary
                return true;
            }
            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max(
                    $vertex1['y'],
                    $vertex2['y']
                ) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) {
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
                if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                    return true;
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections++;
                }
            }
        }
        // If the number of edges we passed through is odd, then it's in the polygon.
        if ($intersections % 2 != 0) {
            return true;
        } else {
            return false;
        }
    }

    function pointOnVertex($point, $vertices)
    {
        foreach ($vertices as $vertex) {
            if ($point == $vertex) {
                return true;
            }
        }
        return false;
    }

    private function assignQuestionToStudentPractise($mainQuestion)
    {
        $this->student->takenPracticeQuestions()
            ->attach($mainQuestion->prepareExamQuestion->id);
    }
}
