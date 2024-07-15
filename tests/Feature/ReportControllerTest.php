<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function test_it_can_generate_a_report()
    {
        $response = $this->get('/generate-report?reportType=requests_by_consumer');

        $response->assertStatus(200);
        $response->assertDownload();
        $response->assertHeader('Content-Type', 'application/csv');
    }

    public function test_it_can_generate_a_report_with_invalid_report_type()
    {
        $response = $this->get('/generate-report?reportType=invalid');

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    public function test_it_can_generate_a_report_with_no_report_type()
    {
        $response = $this->get('/generate-report');

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }
}
