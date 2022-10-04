<?php
Route::group(['prefix'=>'learning-performance','as'=>'learningPerformance.',
                'namespace' => '\App\OurEdu\LearningPerformance\StudentTeacher\Controllers\Api'], function () {

    Route::get('subject-performance/{studentId}/{subjectId}', 'LearningPerformanceController@getStudentSubjectPerformance')
        ->name('get.studentSubjectPerformance');

    Route::get('exam-performance/{examId}', 'LearningPerformanceController@getExamPerformance')
        ->name('get.examPerformance');

    Route::get('subjects-performance/{studentId}', 'LearningPerformanceController@getStudentAllSubjectsPerformance')
        ->name('get.getStudentAllSubjectsPerformance');
});
