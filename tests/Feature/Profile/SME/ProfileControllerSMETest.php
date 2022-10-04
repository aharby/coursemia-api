<?php


namespace Tests\Feature\profile\SME;

use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\Schools\School;
use App\OurEdu\Users\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileControllerSMETest extends TestCase
{
    public function test_post_edit_profile()
    {
        dump('test_post_edit_profile');

        $user = $this->authSME();

        $row = [
            "data" => [
                'id'=>null,
                'type'=>"user",
                'attributes' => [
                    'first_name' => 'first name',
                    'last_name' => 'last name',
                    'language' => 'ar',
                    'mobile'=>$user->mobile,
                    'email'=>$user->email,
                    ],
            ],
        ];
        $resource = $this->postJson('api/v1/en/profile/update-profile', $row, $this->loginUsingHeader($user));
//         dd($resource);
        $record = User::find($user->id);
        // dd($record);

        $this->assertEquals('first name', $record->first_name);
    }

    public function test_get_profile_for_student()
    {
        dump('test_get_profile_for_student');

        $student = $this->authStudent();
        $this->apiSignIn($student);

        $response = $this->getJson('/api/v1/en/profile')->assertOk();
        $response->assertJsonStructure([
                        'data' =>
                           [
                               'type',
                               'id',
                               'attributes' =>
                                   [
                                       'first_name',
                                       'last_name',
                                       'language',
                                       'mobile',
                                       'profile_picture',
                                       'email',
                                   ],
                           ]
                        ]);
    }

    public function test_post_profile_for_student()
    {
        dump('test_post_edit_profile');

        $user = $this->authStudent();

        $row = [
            "data" => [
                'id'=>null,
                'type'=>"user",
                'attributes' => [
                    'first_name' => 'first name',
                    'last_name' => 'last name',
                    'language' => 'en',
                    'mobile'=>$user->mobile,
                    'email'=>$user->email,
                    "birth_date"=>"1994-05-17",
                    "educational_system" => EducationalSystem::first()->id,
                    "class" => factory(GradeClass::class)->create()->id,
                    "country" => factory(Country::class)->create()->id,
                    "academical_year"=> Option::where('type', OptionsTypes::ACADEMIC_YEAR)->first()->id,
                    "school"=> factory(School::class)->create()->id
                ],
            ],
        ];
        $resource = $this->postJson('api/v1/en/profile/update-profile', $row, $this->loginUsingHeader($user));
        $record = User::find($user->id);

        $this->assertEquals('first name', $record->first_name);
    }

    public function test_post_update_password()
    {
        dump('test_post_update_password');
        $user = factory(User::class)->create([
            'password' => '12345678'
        ]);
        $newPassword = '12345678';
        $row = [
            "data" => [
                'id'=>null,
                'type'=>"user",
                'attributes' => [
                    'old_password' => '12345678',
                    'password' => $newPassword,
                    'password_confirmation' => $newPassword,
                ],
            ],
        ];
        $response = $this->postJson('api/v1/en/profile/update-password', $row, $this->loginUsingHeader($user));
        $this->assertTrue(\Hash::check($newPassword, $user->password));

    }
}
