<?php

namespace Database\Seeders;

use App\OurEdu\Users\User;
use Illuminate\Database\Seeder;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userTypes = [
            'admin',
            'super_admin',
            'student',
            'parent',
            'content_author',
            'instructor',
            'student_teacher',
            'sme'
        ];

        foreach ($userTypes as $type) {
            User::factory()->create([
                'first_name'    => $type,
                'type'    => $type,
                'email'    =>    "$type@ta3lom.com"
            ]);
        }
    }
}
