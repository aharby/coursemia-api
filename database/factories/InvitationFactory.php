<?php

namespace Database\Factories;

use App\OurEdu\Invitations\Enums\InvitationEnums;
use App\OurEdu\Invitations\Models\Invitation;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvitationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invitation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create(['type' => UserEnums::STUDENT_TYPE]);
        $receiver = User::factory()->create(['type' => UserEnums::STUDENT_TYPE]);

        return [
            'sender_id'    =>    $user->id,
            'receiver_email'    =>    $receiver->email,
            'status'    =>    InvitationEnums::PENDING,
            'invitable_type'    =>    'user',
            'invitable_id'    =>    $user->id,
        ];
    }
}
