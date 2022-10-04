<?php

namespace Tests\Feature\Payments\Parent;

use App\OurEdu\Payments\Models\PaymentTransaction;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentApiControllerTest extends TestCase
{
    use WithFaker;

    public function test_parent_can_list_transactions()
    {
        $this->disableExceptionHandling();

        dump('test_parent_can_list_transactions');

        $parent = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $student = create(User::class, ['type' => UserEnums::STUDENT_TYPE]);


        $transaction = PaymentTransaction::create([
            'sender_id' =>  $parent->id,
            'receiver_id' =>  $student->id,
            'amount'    =>  10
        ]);

        $response = $this->getJson(route('api.parent.payments.index', ['language' => 'en']), $this->loginUsingHeader($parent))
            ->assertOk()
            ->assertJsonFragment(['total' => 1])
            ->assertJsonStructure([
                "data" => [
                ]
            ]);
    }

    public function test_parent_can_add_money_to_student_wallet()
    {
        dump('test_parent_can_add_money_to_student_wallet');

        $parent = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $student = create(Student::class, ['wallet_amount' => 0.00]);
        $user = $student->user;

        $parent->students()->sync($user);

        $this->assertEquals(0, $student->wallet_amount);

        $request_data = [
            'data'  =>  [
                'type'  =>  'user',
                'id'    =>  null,
                'attributes'    =>  [
                    'amount' =>  20
                ]
            ]
        ];

        $response = $this->postJson(route('api.parent.payments.submitTransaction', ['language' => 'en', 'student_id' => $student->id]), $request_data, $this->loginUsingHeader($parent))
            ->assertStatus(200);

        $this->assertEquals(20, $student->fresh()->wallet_amount);
    }
}
