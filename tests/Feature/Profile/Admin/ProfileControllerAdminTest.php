<?php


namespace Tests\Feature\profile\Admin;

use App\OurEdu\Users\User;
use Tests\TestCase;

class ProfileControllerAdminTest extends TestCase
{
    public function test_post_edit_profile()
    {
        dump('test_post_edit_profile');

        $this->authAdmin();
        $user = factory(User::class)->create();
        $user['first_name'] = 'first name';
        $user['last_name'] = 'last name';
        $user['language'] = 'ar';
        $response = $this
        ->put('/profile/admin/edit', $user->toArray());
        $response->assertStatus(302);
        $record = User::find($user->id);
        $this->assertEquals($user['first_name'], $user->first_name);
    }
}
