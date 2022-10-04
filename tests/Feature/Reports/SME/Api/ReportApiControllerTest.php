<?php


namespace Tests\Feature\Reports\SME\Api;

use App\OurEdu\Reports\Report;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ReportApiControllerTest extends TestCase
{
    use WithFaker;

    public function test_list_reports()
    {
        dump('test_list_reports');
        $sme = $this->authSME();
        $this->apiSignIn($sme);
        $report = factory(Report::class)->create();
        $response = $this->get("/api/v1/en/sme/reports");

        $response->assertOk();
        $response->assertJson($response->decodeResponseJson());
        $response->assertJsonStructure([
            'data' => [
            ]
        ]);
    }

    public function test_view_report()
    {
        dump('test_view_report');
        $sme = $this->authSME();
        $this->apiSignIn($sme);

        $report = factory(Report::class)->create();
        $response = $this->get("/api/v1/en/sme/reports/view-report/".$report->id);
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
            ]
        ]);
    }
}
