<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\RolesEnum;
use App\Modules\Users\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    // 1. Migrate guest_devices → users + user_devices
    $guestDevices = DB::table('guest_devices')->get();

    foreach ($guestDevices as $guest) {

        $userId = DB::table('users')->insertGetId([
            'created_at' => $guest->created_at,
            'updated_at' => $guest->updated_at,
        ]);

        // Assign guest role to new user
        DB::table('model_has_roles')->insert([
            'role_id'    => DB::table('roles')
                            ->where('name', RolesEnum::GUEST)
                            ->value('id'),
            'model_type' => User::class,
            'model_id'   => $userId,
        ]);

        // Insert into user_devices
        DB::table('user_devices')->insert([
            'user_id' => $userId,
            'device_id' => $guest->guest_device_id,
            'device_name' => '',
            'device_type' => '',
            'device_token' => null,
            'allow_push_notifications' => false,
            'is_tablet' => false,
            'created_at' => $guest->created_at,
            'updated_at' => $guest->updated_at,
        ]);
    }

    // 2. Update cart_courses schema + data
    Schema::table('cart_courses', function (Blueprint $table) {
        // Rename student_id → user_id
        $table->renameColumn('student_id', 'user_id');
    });

    // Backfill user_id where guest_device_id was set
    $cartCourses = DB::table('cart_courses')->whereNotNull('guest_device_id')->get();

    foreach ($cartCourses as $cart) {
        $userId = DB::table('user_devices')
            ->where('device_id', $cart->guest_device_id)
            ->value('user_id');

        if ($userId) {
            DB::table('cart_courses')
                ->where('id', $cart->id)
                ->update(['user_id' => $userId]);
        }
    }

    // Drop guest_device_id column
    Schema::table('cart_courses', function (Blueprint $table) {
        $table->dropForeign(['guest_device_id']);   // <-- remove FK first
        $table->dropColumn('guest_device_id');      // then drop the column
    });

    // 3. Drop guest_devices table
    Schema::dropIfExists('guest_devices');
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_devices', function (Blueprint $table) {
            //
        });
    }
};
