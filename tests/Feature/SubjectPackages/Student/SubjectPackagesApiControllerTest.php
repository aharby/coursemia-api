<?php

namespace Tests\Feature\SubjectPackages\Student\Api;

use App\OurEdu\SubjectPackages\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubjectPackagesApiControllerTest extends TestCase
{
    public function test_get_list_available_subject_packages()
    {
        dump('test_get_list_available_subject_packages');

        $student = $this->authStudent();
        $this->apiSignIn($student);
        $this->getJson("/api/v1/en/student/subject-packages/")
            ->assertOk();
    }

    public function test_student_subscribe_package()
    {
        dump('test_student_subscribe_package');

        $student = $this->authStudent();
        $this->apiSignIn($student);
        $package = factory(Package::class)->make()->toArray();
        $package['grade_class_id'] = $student->student->class_id;
        $package['educational_system_id'] = $student->student->educational_system_id;
        $package['academical_years_id'] = $student->student->academical_year_id;
        $package['country_id'] = $student->country_id;
        $package = Package::create($package);
        $student = $student->student;
        $student->wallet_amount = $package->price;
        $student->save();
        $response = $this->postJson("/api/v1/en/student/subject-packages/subscribe-package/" . $package->id, []);
        $response->assertOk();

        $this->assertDatabaseHas('packages_subscribed_students', [
            'package_id' => $package->id,
            'student_id' => $student->id,
        ]);
    }
}
