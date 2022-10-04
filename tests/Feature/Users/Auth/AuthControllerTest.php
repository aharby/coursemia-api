<?php


namespace Tests\Feature\Users\Auth;

use App\OurEdu\Schools\School;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class AuthControllerTest extends TestCase
{
    use WithFaker;

    public function test_post_login()
    {
        dump('test_post_login');

        $row = [
            'email' => 'super_admin@super_admin.com',
            'password' => 'password'
        ];
        $response = $this
            ->post('auth/login/admin', $row);
        $response
            ->assertStatus(302);
    }

    public function test_post_forget_password()
    {
        dump('test_post_forget_password');

        $email = factory(User::class)->create();

        $RowForForgetPassword = [
            'email' => $email->email,
        ];
        $response = $this
            ->post('auth/forgot-password', $RowForForgetPassword);

        $token= DB::table('password_resets')->where('email', $RowForForgetPassword['email'])->first()->token;

        $RowForUpdatePassword= [
            'password' => 'password',
        ];
        $response = $this
            ->post("auth/update-password/{$token}", $RowForUpdatePassword)
            ->assertStatus(302);
    }
}
