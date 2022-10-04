<?php

Route::group(['prefix'=>'learning-performance','as'=>'learningPerformance.'], function () {

    Route::get('subject-performance/{studentId}/{subjectId}', '\App\OurEdu\LearningPerformance\Parent\Controllers\Api\LearningPerformanceController@getStudentSubjectPerformance')
        ->name('get.studentSubjectPerformance');

    Route::get('exam-performance/{examId}', '\App\OurEdu\LearningPerformance\Parent\Controllers\Api\LearningPerformanceController@getExamPerformance')
        ->name('get.examPerformance');

    Route::get('subjects-performance/{studentId}', '\App\OurEdu\LearningPerformance\Parent\Controllers\Api\LearningPerformanceController@getStudentAllSubjectsPerformance')
        ->name('get.getStudentAllSubjectsPerformance');

    Route::get('subjects-performance/activityLog/{student}', '\App\OurEdu\LearningPerformance\Parent\Controllers\Api\LearningPerformanceController@getStudentActivityLog')
        ->name('get.getStudentActivityLog');

    Route::get('subjects-performance/packages/{student}', '\App\OurEdu\LearningPerformance\Parent\Controllers\Api\LearningPerformanceController@getStudentPackages')
        ->name('get.getStudentPackages');

    Route::get('subjects-performance/subjects/{student}', '\App\OurEdu\LearningPerformance\Parent\Controllers\Api\LearningPerformanceController@getStudentSubjects')
        ->name('get.getStudentSubjects');
    Route::get('subjects-performance/courses/unsubscribed-top-qudrat/{student}', '\App\OurEdu\LearningPerformance\Parent\Controllers\Api\LearningPerformanceController@getStudentUnsubscribedTopQudratCourses');
    Route::get('subjects-performance/courses/subscribed/{student}', '\App\OurEdu\LearningPerformance\Parent\Controllers\Api\LearningPerformanceController@getStudentSubscribedCourses');

    Route::get('subjects-performance/subjects/qudrat/{student}', '\App\OurEdu\LearningPerformance\Parent\Controllers\Api\LearningPerformanceController@getQudratStudentSubjects')
        ->name('get.getQudratStudentSubjects');

    Route::get('subjects-performance/courses/{student}', '\App\OurEdu\LearningPerformance\Parent\Controllers\Api\LearningPerformanceController@getStudentCourses')
        ->name('get.getStudentCourses');


    Route::get('subject-performance/{studentId}/{subjectId}/activity-log', '\App\OurEdu\LearningPerformance\Parent\Controllers\Api\StudentSubjectLearningPerformanceController@activityLogPerformance')
        ->name('get.studentSubjectPerformanceActivityLog');

    Route::get('subject-performance/{studentId}/{SubjectId}/exams','\App\OurEdu\LearningPerformance\Parent\Controllers\Api\StudentSubjectLearningPerformanceController@examPerformance')
        ->name('get.studentSubjectPerformanceExam');

    Route::get('subject-performance/{studentId}/{SubjectId}/times','\App\OurEdu\LearningPerformance\Parent\Controllers\Api\StudentSubjectLearningPerformanceController@timesPerformance')
        ->name('get.studentSubjectPerformanceTimes');

    Route::get('subject-performance/{studentId}/{SubjectId}/feedback','\App\OurEdu\LearningPerformance\Parent\Controllers\Api\StudentSubjectLearningPerformanceController@studentFeedback')
        ->name('get.studentSubjectPerformanceFeedback');

    Route::get('subjects-performance/courses/top-qudrat/{student}', '\App\OurEdu\LearningPerformance\Parent\Controllers\Api\LearningPerformanceController@getStudentSubscribedAndUnsubscribedTopQudratCourses');
});
