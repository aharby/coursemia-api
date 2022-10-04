<?php

namespace App\OurEdu\Subscribes\UseCases;

interface SubscriptionUseCaseInterface
{
    public function subscripeSubject($studentId, $subscriptionSubject);

    /**
     * @param $studentId
     * @param $subscriptionCourse
     * @return mixed
     */
    public function subscripeCourse($studentId, $subscriptionCourse);

    /**
     * @param $studentId
     * @param $subjectPackage
     * @return mixed
     */
    public function subscribeSubjectPackage($studentId,$subjectPackage);
}
