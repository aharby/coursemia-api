<?php


namespace Tests\Feature\Users\Auth\Api;

use App\OurEdu\AcademicYears\AcademicYear;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\Schools\School;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Arr;

class AuthApiControllerTest extends TestCase
{
    use WithFaker;

    public function test_post_register_parent()
    {
        dump('test_api_post_register_parent');

        $country_id = factory(Country::class)->create(['is_active'=> 1])->id;
        // uploading a file
        $file = UploadedFile::fake()->image('avatar.jpg');
        $fileResponse = $this->json('post', '/api/v1/en/image', ['images' => [$file]]);
        $fileContent = json_decode($fileResponse->getContent());
        // getting the file ID
        $myFileId = 0;

        foreach ($fileContent as $content) {
            foreach ($content as $c) {
                // getting the id of one file
                $myFileId = $c->id;
            };
        }

        $email = $this->faker->email;

        $request =
            [
                "data" => [
                    "type" => "user",
                    "id" => "null",
                    "attributes" => [
                        "user_type" => UserEnums::PARENT_TYPE,
                        "first_name" => "parent",
                        "last_name" => "JOE",
                        "email" => $email,
                        "birth_date" => "1996-01-12",
                        "country_id" => $country_id ,
                        "mobile" => "01208" . random_int(111111, 999999),
                        "password" => "12345678",
                        "password_confirmation" => "12345678",
                    ],
                    [
                        'relationships' =>
                            [
                                'attachMedia' =>
                                    [
                                        'type' => 'attach_media',
                                        'id' => $myFileId,
                                    ]
                            ],
                    ]

                ]
            ];
        $response = $this->postJson("/api/v1/en/auth/register", $request);
        $response->assertOk();
        $this->assertDatabaseHas('users', [
            "type" => UserEnums::PARENT_TYPE,
            "first_name" => "parent",
            "last_name" => "JOE",
            "email" => $email,
        ]);
    }

    public function test_post_register_student()
    {
        dump('test_api_post_register_student');

        $country_id = factory(Country::class)->create(['is_active'=> 1])->id;
        $edu_system_id = factory(EducationalSystem::class)->create(['is_active'=> 1])->id;
        $academic_year_id =  factory(Option::class)->create(['type' => OptionsTypes::ACADEMIC_YEAR])->first()->id;
        $school_id = factory(School::class)->create([
            'is_active'=> 1,
           'address' => '73 abo',
           'country_id' => $country_id,
           'educational_system_id' => $edu_system_id])->id;
        $class_id = factory(GradeClass::class)->create()->id;

        // uploading a file
        $file = UploadedFile::fake()->image('avatar.jpg');
        $fileResponse = $this->json('post', '/api/v1/en/image', ['images' => [$file]]);
        $fileContent = json_decode($fileResponse->getContent());
        // getting the file ID
        $myFileId = 0;
        foreach ($fileContent as $content) {
            foreach ($content as $c) {
                // getting the id of one file
                $myFileId = $c->id;
            };
        }

        $email = $this->faker->email();

        $request =
            [
                "data" => [
                    "type" => "user",
                    "id" => "null",
                    "attributes" => [
                        "user_type" => UserEnums::STUDENT_TYPE,
                        "first_name" => "student",
                        "last_name" => "mm",
                        "email" => $email,
                        "country_id" => $country_id,
                        "school_id" => $school_id,
                        "class_id" => $class_id,
                        "educational_system_id" => $edu_system_id,
                        "academical_year_id" => $academic_year_id,
                        "mobile" => "0120" . random_int(100000, 999999),
                        "password" => "12345678",
                        "password_confirmation" => "12345678",
                    ],
                    [
                        'relationships' =>
                        [
                            'attachMedia' =>
                            [
                                'type' => 'attach_media',
                                'id' => $myFileId,
                            ]
                        ],
                    ]

                ]
            ];
        $response = $this->postJson("/api/v1/en/auth/register", $request);
        $response->assertOk();
        $this->assertDatabaseHas('students', [
            "school_id" => $school_id,
            "class_id" => $class_id,
            "educational_system_id" => $edu_system_id,
            "academical_year_id" => $academic_year_id,
        ]);
    }

    public function test_activate_parent()
    {
        //if you want to  test other user make other variable and override the type and test it .
        dump('test_api_activate_parent');
        $user = factory(User::class)->create([
            'type' => UserEnums::PARENT_TYPE,
            'confirm_token' => Str::random(60)
        ]);
        $response = $this->getJson('/api/v1/en/auth/activate/' . $user->confirm_token);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'confirm_token' => null,
            'confirmed' => true,
        ]);
    }

    public function test_activate_student()
    {
        //if you want to  test other user make other variable and override the type and test it .
        dump('test_api_activate_student');
        $user = factory(User::class)->create([
            'type' => UserEnums::STUDENT_TYPE,
            'confirm_token' => Str::random(60)
        ]);
        $response = $this->getJson('/api/v1/en/auth/activate/' . $user->confirm_token);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'confirm_token' => null,
            'confirmed' => true,
        ]);
    }

    public function test_post_api_login()
    {
        dump('test_post_api_login');
        $user = factory(User::class)->create([
            'password' => '12345678',
            'type' => UserEnums::PARENT_TYPE,
            'confirm_token' => Str::random(60)
        ]);
        $response = $this->get('/api/v1/en/auth/activate/' . $user->confirm_token);
        $request =
            [
                "data" => [
                    "type" => "user",
                    "id" => "null",
                    "attributes" => [
                        "email" => $user->email,
                        "password" => '12345678',
                    ],
                ]
            ];
        $response = $this->postJson("/api/v1/en/auth/login", $request)
            ->assertOk();
    }

    public function test_api_logout()
    {
        dump('test_api_logout');

        $user = factory(User::class)->create();

        $user->firebaseTokens()->create([
            'device_token'  =>  $device_token = str_random(20),
            'fingerprint' =>  $token = str_random(60)
        ]);

        $this->assertCount(1, $user->firebaseTokens);

        $request_data = [
            "data" => [
                    "type" => "user",
                    "id" => "null",
                    "attributes" => [
                        "device_token" => $device_token,
                    ],
                ]
        ];

        $response = $this->Json('post', '/api/v1/en/auth/logout', $request_data, $this->loginUsingHeader($user))
            ->assertStatus(200)
            ->assertJsonFragment(['message' => trans('api.Successfully Logged Out')])
            ->assertJsonStructure([
                "meta" => [
                    'message'
                ]
            ]);

        $this->assertCount(0, $user->fresh()->firebaseTokens);
    }

    public function test_user_login_or_register_facebook()
    {
        dump('test_login_or_register_facebook');
        $userData = [
            'data'  => [
                'type'  => 'user',
                'id'  => null,
                'attributes'    =>  [
                    'user_type'   => Arr::random([UserEnums::PARENT_TYPE, UserEnums::STUDENT_TYPE]),
                    'token'  => 'EAAF0sN1SZClMBADQKG9p52BjdyqciKbZC98rZBEcb4z5U6SUTNhAzrvvyPd9RXKORC26eIwwAQ8oBVXYfJGLdLVe4UucubZCfJnDTr30BVGH9ekgT4T0Vf3KshLsjZAMMOdhsGhsZAOA2EMW5XveYbkax4HdeaIplADdzVyaU40nQdyVesYsOyQsbIZB3NZBJZChhjRBpqfqpWQZDZD',
                ]
            ]
        ];
        $this->json('post', '/api/v1/en/auth/provider/facebook', $userData)
            ->assertStatus(200)
            ->assertJsonStructure([
                'meta' => [
                    'token'
                ]
            ]);
    }

    public function test_change_language()
    {
        dump('test_change_language');
        $student = $this->authStudent();
        $this->apiSignIn($student);
        $request = [
            "data" => [
                "type" => "lang",
                "id" => "null",
                "attributes" => [
                    "lang_slug" => array_rand(languages(), 1)
                ]
            ]
        ];
        $response = $this->postJson('api/v1/en/auth/change-language', $request);
        $response
            ->assertOk()
            ->assertJsonFragment(['message' => trans('auth.Language changed successfully')]);
    }

    public function test_user_stores_fcm_token()
    {
        dump('test_user_stores_fcm_token');

        $user = factory(User::class)->create();

        $this->assertCount(0, $user->firebaseTokens);

        $request_data = [
            "data" => [
                    "type" => "user",
                    "id" => "null",
                    "attributes" => [
                        'fingerprint' =>  $token = str_random(60),
                        'device_token'  =>  $device_token = str_random(20)
                    ],
                ]
        ];

        $response = $this->Json('post', '/api/v1/en/auth/fcm-tokens', $request_data, $this->loginUsingHeader($user))
            ->assertStatus(200)
            ->assertJsonFragment(['message' => trans('auth.Token stored successfully')])
            ->assertJsonStructure([
                "meta" => [
                    'message'
                ]
            ]);

        $this->assertCount(1, $user->fresh()->firebaseTokens);

        $this->assertDatabaseHas('firebase_tokens', [
            "fingerprint" => $token,
            "device_token" => $device_token,
            "user_id" => $user->id,
        ]);
    }
}
