<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'instructor',
        'as' => 'instructor.',
        'namespace' => '\App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Controllers\Api'
    ],
    function () {
        Route::post('create/{course}', 'CourseHomeworkController@store')
            ->name('post.create_course_homework');

        Route::put('edit/{courseHomework}/{course}', 'CourseHomeworkController@update')
            ->name('put.edit_course_homework');
        Route::post("{courseHomework}/questions", "CourseHomeworkQuestionController@store")
            ->name('post.create_course_homework_question');
        Route::get("{courseHomework}/questions/{questionBank}/view", "CourseHomeworkQuestionController@view")->name(
            'view.course_homework_question'
        );
        Route::put('{courseHomework}/review-essay/{answer}', 'CourseHomeworkQuestionController@reviewEssay')
            ->name('put.review_essay_question');

        Route::delete('{courseHomework}/question/delete/{question}', 'CourseHomeworkQuestionController@delete')
            ->name('delete.course_homework_question');

        Route::get("{courseHomework}/questions/list", "CourseHomeworkQuestionController@list")->name(
            'get.course_homework_questions'
        );
        Route::get('/list', 'CourseHomeworkController@index')
            ->name('get.courses.list');

        Route::delete('/delete/{courseHomework}', 'CourseHomeworkController@delete')
            ->name('delete.course_homework');

        Route::post('/publish/{courseHomework}', 'CourseHomeworkController@publish')
            ->name('post.publish_course_homework');
        Route::get('/list/export', 'CourseHomeworkController@ExportIndexData')
            ->name('get.export');
        Route::get('/{courseHomework}/list-students-scores', 'CourseHomeworkController@listStudentsScores')
            ->name('get.list_students_scores');
        Route::get("export/students-grades/{courseHomework}", "CourseHomeworkController@exportStudentsGrades")
            ->name("export.grades");
        Route::get('/{courseHomework}/export-students-scores', 'CourseHomeworkController@exportStudentsScores')
            ->name('get.export_students_scores');

        Route::get('/view/{courseHomework}', 'CourseHomeworkController@show')
            ->name('get.view_course_homework');

        Route::get('/preview/{courseHomework}', 'CourseHomeworkController@preview')
            ->name('get.preview_course_homework');
        Route::post("retake/{courseHomework}",  "CourseHomeworkController@retake")->name("retake_course_homework");
        Route::get('/{courseHomework}/get-answers/{student}', 'CourseHomeworkController@getStudentHomeworkAnswers')
            ->name('get.get_student_course_homework_answers');
        Route::get('/{courseHomework}/get-answers-paginate/{student}', 'CourseHomeworkController@getStudentAnswersSolved')
            ->name('get.get_student_answers_solved');
    });
