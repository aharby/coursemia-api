<?php

namespace Tests\Feature\Notifications;

use Tests\TestCase;

class NotificationApiControllerTest extends TestCase
{
    public function test_list_all_notifications() {
        dump('test_list_all_notifications');
        $user = $this->authParent();
        $response = $this->getJson('api/v1/en/parent/notifications/', $this->loginUsingHeader($user));
        $response->assertOk();
    }
}
