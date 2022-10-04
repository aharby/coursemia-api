<?php


namespace Tests\Feature\LookUp\Api;

use Tests\TestCase;

class LookUpControllerTest extends TestCase
{
    public function test_look_up()
    {
        dump('test_look_up');
        $user = $this->authSME();

        $this->getJson(
            'api/v1/en/look-up?include=academicYear,classes,countries,educationalSystems,schools&filter[educational_system_id]=2&filter[country_id]=2',
            $this->loginUsingHeader($user)
        )->assertOk();
    }
}
