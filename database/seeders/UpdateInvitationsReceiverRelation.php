<?php

namespace Database\Seeders;

use App\OurEdu\Invitations\Models\Invitation;
use App\OurEdu\Users\User;
use Illuminate\Database\Seeder;

class UpdateInvitationsReceiverRelation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $invitations = Invitation::all();
        foreach ($invitations as $invitation){
            $user = User::where('email', $invitation->receiver_email)->where('username',null)->first();
            if (!$user){
                $user = User::where('email', $invitation->receiver_email)->first();
            }
            if ($user){
                $invitation->update(['receiver_id'=>$user->id]);
            }
        }
    }
}
